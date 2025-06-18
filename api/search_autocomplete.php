<?php
// api/search_autocomplete.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include_once '../config/koneksi.php';

header('Content-Type: application/json');

$query = $_GET['q'] ?? '';
$query = trim($query);

$suggestions = [];

if (empty($query) || strlen($query) < 2) { // Minimum 2 karakter untuk memulai saran
    echo json_encode([]);
    exit;
}

try {
    // Escaping query for LIKE clause
    $safe_query_like = '%' . $query . '%';

    // Prepare query for FULLTEXT search (for better relevance and basic typo tolerance)
    // Use IN BOOLEAN MODE for more flexibility, including partial word matching with '*'
    $safe_query_fulltext = $query . '*'; // Allows matching "hon" for "honda"

    // Combine results from brands, models, and vehicles
    // Use UNION ALL for performance (no duplicate checking overhead from DB, handled in PHP)
    $stmt = $koneksi->prepare("
        (SELECT
            CONCAT(b.name, ' ', vm.name) AS suggestion_text,
            'Kendaraan' AS type,
            v.id AS vehicle_id,
            v.price_displayed,
            (
                SELECT photo_path
                FROM vehicle_photos
                WHERE vehicle_id = v.id AND deleted_at IS NULL
                ORDER BY is_cover DESC, id ASC
                LIMIT 1
            ) AS image
        FROM
            vehicles v
        JOIN
            vehicle_models vm ON v.vehicle_model_id = vm.id
        JOIN
            brands b ON vm.brand_id = b.id
        WHERE
            v.status = 'available'
            AND v.stnk_deadline >= CURDATE()
            AND v.deleted_at IS NULL
            AND vm.deleted_at IS NULL
            AND b.deleted_at IS NULL
            AND (
                CONCAT(b.name, ' ', vm.name) LIKE :like_vehicle
                OR MATCH(b.name, vm.name) AGAINST(:fulltext_vehicle IN BOOLEAN MODE)
                OR v.description LIKE :like_description
                OR MATCH(v.description) AGAINST(:fulltext_description IN BOOLEAN MODE)
            )
        )
        UNION ALL
        (SELECT
            name AS suggestion_text,
            'Merek' AS type,
            NULL AS vehicle_id,
            NULL AS price_displayed,
            NULL AS image
        FROM
            brands
        WHERE
            deleted_at IS NULL
            AND (
                name LIKE :like_brand
                OR MATCH(name) AGAINST(:fulltext_brand IN BOOLEAN MODE)
            )
        )
        UNION ALL
        (SELECT
            name AS suggestion_text,
            'Model' AS type,
            NULL AS vehicle_id,
            NULL AS price_displayed,
            NULL AS image
        FROM
            vehicle_models
        WHERE
            deleted_at IS NULL
            AND (
                name LIKE :like_model
                OR MATCH(name) AGAINST(:fulltext_model IN BOOLEAN MODE)
            )
        )
        LIMIT 10 -- Batasi jumlah hasil untuk performa
    ");

    $stmt->bindValue(':like_vehicle', $safe_query_like);
    $stmt->bindValue(':fulltext_vehicle', $safe_query_fulltext);
    $stmt->bindValue(':like_description', $safe_query_like);
    $stmt->bindValue(':fulltext_description', $safe_query_fulltext);
    $stmt->bindValue(':like_brand', $safe_query_like);
    $stmt->bindValue(':fulltext_brand', $safe_query_fulltext);
    $stmt->bindValue(':like_model', $safe_query_like);
    $stmt->bindValue(':fulltext_model', $safe_query_fulltext);

    $stmt->execute();

    $raw_suggestions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $seen_suggestions = [];
    $base_image_url = './storage/';

    foreach ($raw_suggestions as $row) {
        // Normalize image path for frontend
        if ($row['type'] === 'Kendaraan' && !empty($row['image'])) {
            $image_path = $base_image_url . $row['image'];
            // Check if file exists to prevent broken images
            if (!file_exists(__DIR__ . '/../' . $image_path)) {
                $image_path = './assets/images/placeholder.png';
            }
            $row['image'] = $image_path;
        } else {
            $row['image'] = './assets/images/placeholder.png'; // Default placeholder for non-vehicle suggestions
        }

        // Remove duplicates based on suggestion_text
        if (!isset($seen_suggestions[$row['suggestion_text']])) {
            $suggestions[] = $row;
            $seen_suggestions[$row['suggestion_text']] = true;
        }
    }

    echo json_encode($suggestions);

} catch (PDOException $e) {
    error_log('Database Error in search_autocomplete: ' . $e->getMessage());
    echo json_encode(['error' => 'Gagal mengambil saran pencarian.']);
}
?>