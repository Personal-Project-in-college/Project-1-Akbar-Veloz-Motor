<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include_once './config/koneksi.php';

$search_query = $_GET['q'] ?? '';
$type_vehicle_filter = $_GET['type_vehicle'] ?? '';
$brand_id_filter = $_GET['brand_id'] ?? '';
$min_year_filter = $_GET['min_year'] ?? '';
$max_year_filter = $_GET['max_year'] ?? '';
$min_price_filter = $_GET['min_price'] ?? '';
$max_price_filter = $_GET['max_price'] ?? '';
$min_kilometer_filter = $_GET['min_kilometer'] ?? '';
$max_kilometer_filter = $_GET['max_kilometer'] ?? '';
$min_cc_filter = $_GET['min_cc'] ?? '';
$max_cc_filter = $_GET['max_cc'] ?? '';

$vehicles = [];
$brands = [];
$min_max_values = [
    'year' => ['min' => null, 'max' => null],
    'price' => ['min' => null, 'max' => null],
    'kilometer' => ['min' => null, 'max' => null],
    'cc_engine' => ['min' => null, 'max' => null]
];
$db_error = null;

try {
    // Fetch all brands for the filter dropdown
    $stmt_brands = $koneksi->query("SELECT id, name FROM brands WHERE deleted_at IS NULL ORDER BY name ASC");
    $brands = $stmt_brands->fetchAll(PDO::FETCH_ASSOC);

    // Fetch min/max values for filters
    $stmt_min_max = $koneksi->query("
        SELECT
            MIN(YEAR(production_year)) AS min_year,
            MAX(YEAR(production_year)) AS max_year,
            MIN(price_displayed) AS min_price,
            MAX(price_displayed) AS max_price,
            MIN(kilometer) AS min_kilometer,
            MAX(kilometer) AS max_kilometer,
            MIN(cc_engine) AS min_cc,
            MAX(cc_engine) AS max_cc
        FROM vehicles
        WHERE status = 'available' AND deleted_at IS NULL
    ");
    $min_max_values_db = $stmt_min_max->fetch(PDO::FETCH_ASSOC);
    if ($min_max_values_db) {
        $min_max_values['year']['min'] = $min_max_values_db['min_year'];
        $min_max_values['year']['max'] = $min_max_values_db['max_year'];
        $min_max_values['price']['min'] = $min_max_values_db['min_price'];
        $min_max_values['price']['max'] = $min_max_values_db['max_price'];
        $min_max_values['kilometer']['min'] = $min_max_values_db['min_kilometer'];
        $min_max_values['kilometer']['max'] = $min_max_values_db['max_kilometer'];
        $min_max_values['cc_engine']['min'] = $min_max_values_db['min_cc'];
        $min_max_values['cc_engine']['max'] = $min_max_values_db['max_cc'];
    }


    // Build the main vehicle query
    $sql = "
        SELECT
            v.id,
            v.type_vehicle,
            v.price_displayed,
            CONCAT(b.name, ' ', vm.name) AS display_name,
            v.production_year,
            v.kilometer,
            v.cc_engine,
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
    ";

    $params = [];

    if (!empty($search_query)) {
        $sql .= " AND (CONCAT(b.name, ' ', vm.name) LIKE :search_query OR v.description LIKE :search_query)";
        $params[':search_query'] = '%' . $search_query . '%';
    }

    if (!empty($type_vehicle_filter)) {
        $sql .= " AND v.type_vehicle = :type_vehicle";
        $params[':type_vehicle'] = $type_vehicle_filter;
    }

    if (!empty($brand_id_filter)) {
        $sql .= " AND b.id = :brand_id";
        $params[':brand_id'] = $brand_id_filter;
    }

    if (!empty($min_year_filter)) {
        $sql .= " AND YEAR(v.production_year) >= :min_year";
        $params[':min_year'] = $min_year_filter;
    }
    if (!empty($max_year_filter)) {
        $sql .= " AND YEAR(v.production_year) <= :max_year";
        $params[':max_year'] = $max_year_filter;
    }

    if (!empty($min_price_filter)) {
        $sql .= " AND v.price_displayed >= :min_price";
        $params[':min_price'] = $min_price_filter;
    }
    if (!empty($max_price_filter)) {
        $sql .= " AND v.price_displayed <= :max_price";
        $params[':max_price'] = $max_price_filter;
    }

    if (!empty($min_kilometer_filter)) {
        $sql .= " AND v.kilometer >= :min_kilometer";
        $params[':min_kilometer'] = $min_kilometer_filter;
    }
    if (!empty($max_kilometer_filter)) {
        $sql .= " AND v.kilometer <= :max_kilometer";
        $params[':max_kilometer'] = $max_kilometer_filter;
    }

    if (!empty($min_cc_filter)) {
        $sql .= " AND v.cc_engine >= :min_cc";
        $params[':min_cc'] = $min_cc_filter;
    }
    if (!empty($max_cc_filter)) {
        $sql .= " AND v.cc_engine <= :max_cc";
        $params[':max_cc'] = $max_cc_filter;
    }

    $sql .= " ORDER BY v.created_at DESC";

    $stmt = $koneksi->prepare($sql);
    $stmt->execute($params);
    $vehicles = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $base_image_url = './storage/';
    foreach ($vehicles as &$vehicle) {
        if (!empty($vehicle['image']) && file_exists($base_image_url . $vehicle['image'])) {
            $vehicle['image'] = $base_image_url . $vehicle['image'];
        } else {
            $vehicle['image'] = './assets/images/placeholder.png';
        }
    }
    unset($vehicle);
} catch (PDOException $e) {
    error_log('Database Error: ' . $e->getMessage());
    $db_error = "Gagal memuat data dari server. Silakan coba lagi nanti.";
}

function render_vehicle_card($vehicle)
{
    $formattedPrice = 'Rp ' . number_format($vehicle['price_displayed'], 0, ',', '.');
    $detailUrl = 'detail.php?id=' . htmlspecialchars($vehicle['id']);
    $imageUrl = htmlspecialchars($vehicle['image']);

    // Original display name
    $originalDisplayName = htmlspecialchars($vehicle['display_name']);

    $displayName = $originalDisplayName;
    $maxTitleLength = 19;
    if (mb_strlen($originalDisplayName) > $maxTitleLength) {
        $displayName = mb_substr($originalDisplayName, 0, $maxTitleLength) . '...';
    }

    $productionYear = isset($vehicle['production_year']) ? htmlspecialchars(date('Y', strtotime($vehicle['production_year']))) : 'N/A';
    $kilometer = isset($vehicle['kilometer']) ? htmlspecialchars(number_format($vehicle['kilometer'])) : 'N/A';
    $ccEngine = isset($vehicle['cc_engine']) ? htmlspecialchars($vehicle['cc_engine']) : 'N/A';


    echo '
    <div class="vehicle-card">
      <div class="card-image-wrapper" style="position: relative;">
        <img src="' . $imageUrl . '" alt="' . $originalDisplayName . '" onerror="this.onerror=null;this.src=\'./assets/images/placeholder.png\';">
        <button class="save-btn" onclick="saveToWishlist(this)"
                data-id="' . htmlspecialchars($vehicle['id']) . '"
                data-name="' . $originalDisplayName . '"
                data-price="' . $formattedPrice . '"
                data-image="' . $imageUrl . '"
                data-detail-url="' . $detailUrl . '">
                  <svg width="64px" height="64px" viewBox="0 -0.5 25 25" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path fill-rule="evenodd" clip-rule="evenodd" d="M12.4997 18.9911L9.5767 15.9911L6.6767 12.9911C5.10777 11.3331 5.10777 8.73809 6.6767 7.08009C7.44494 6.34175 8.48548 5.95591 9.54937 6.01489C10.6133 6.07387 11.6048 6.57236 12.2867 7.39109L12.4997 7.60009L12.7107 7.38209C13.3926 6.56336 14.3841 6.06487 15.448 6.00589C16.5119 5.94691 17.5525 6.33275 18.3207 7.07109C19.8896 8.72909 19.8896 11.3241 18.3207 12.9821L15.4207 15.9821L12.4997 18.9911Z" stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
        </button>
      </div>
      <div class="card-content">
        <h3>' . $displayName . '</h3>
        <p>Mulai dari ' . $formattedPrice . '</p>
        <p class="vehicle-specs">Tahun: ' . $productionYear . ' | KM: ' . $kilometer . ' | CC: ' . $ccEngine . '</p>
        <a href="' . $detailUrl . '" class="btn-secondary">Detail</a>
      </div>
    </div>';
}
?>

<!DOCTYPE html>
<html lang="id" translate="no">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Hasil Pencarian - Akbar Veloz Motor</title>
    <link rel="stylesheet" href="./css/style.css" />
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans&display=swap" rel="stylesheet">
</head>

<body>
    <?php include("./layouts/navbar.php"); ?>

    <main class="container search-page">
        <section>
            <h2>Hasil Pencarian Kendaraan</h2>

            <div class="search-layout">
                <aside class="filter-sidebar" id="filterSidebar">
                    <h3>Filter Pencarian</h3>
                    <form action="search.php" method="GET" class="filter-form">
                        <input type="hidden" name="q" value="<?php echo htmlspecialchars($search_query); ?>">

                        <div class="filter-group">
                            <label for="type_vehicle">Jenis Kendaraan:</label>
                            <select id="type_vehicle" name="type_vehicle">
                                <option value="">Semua</option>
                                <option value="motorcycle" <?php echo ($type_vehicle_filter == 'motorcycle') ? 'selected' : ''; ?>>Motor</option>
                                <option value="car" <?php echo ($type_vehicle_filter == 'car') ? 'selected' : ''; ?>>Mobil</option>
                            </select>
                        </div>

                        <div class="filter-group">
                            <label for="brand_id">Merek:</label>
                            <select id="brand_id" name="brand_id">
                                <option value="">Semua Merek</option>
                                <?php foreach ($brands as $brand): ?>
                                    <option value="<?php echo htmlspecialchars($brand['id']); ?>" <?php echo ($brand_id_filter == $brand['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($brand['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="filter-group">
                            <label>Tahun Produksi:</label>
                            <div class="range-inputs">
                                <input type="number" name="min_year" placeholder="Min" value="<?php echo htmlspecialchars($min_year_filter); ?>" min="<?php echo $min_max_values['year']['min']; ?>" max="<?php echo $min_max_values['year']['max']; ?>">
                                <span>-</span>
                                <input type="number" name="max_year" placeholder="Max" value="<?php echo htmlspecialchars($max_year_filter); ?>" min="<?php echo $min_max_values['year']['min']; ?>" max="<?php echo $min_max_values['year']['max']; ?>">
                            </div>
                        </div>

                        <div class="filter-group">
                            <label>Harga (Rp):</label>
                            <div class="range-inputs">
                                <input type="number" name="min_price" placeholder="Min" value="<?php echo htmlspecialchars($min_price_filter); ?>" min="<?php echo $min_max_values['price']['min']; ?>" max="<?php echo $min_max_values['price']['max']; ?>">
                                <span>-</span>
                                <input type="number" name="max_price" placeholder="Max" value="<?php echo htmlspecialchars($max_price_filter); ?>" min="<?php echo $min_max_values['price']['min']; ?>" max="<?php echo $min_max_values['price']['max']; ?>">
                            </div>
                        </div>

                        <div class="filter-group">
                            <label>Kilometer (KM):</label>
                            <div class="range-inputs">
                                <input type="number" name="min_kilometer" placeholder="Min" value="<?php echo htmlspecialchars($min_kilometer_filter); ?>" min="<?php echo $min_max_values['kilometer']['min']; ?>" max="<?php echo $min_max_values['kilometer']['max']; ?>">
                                <span>-</span>
                                <input type="number" name="max_kilometer" placeholder="Max" value="<?php echo htmlspecialchars($max_kilometer_filter); ?>" min="<?php echo $min_max_values['kilometer']['min']; ?>" max="<?php echo $min_max_values['kilometer']['max']; ?>">
                            </div>
                        </div>

                        <div class="filter-group">
                            <label>Mesin (CC):</label>
                            <div class="range-inputs">
                                <input type="number" name="min_cc" placeholder="Min" value="<?php echo htmlspecialchars($min_cc_filter); ?>" min="<?php echo $min_max_values['cc_engine']['min']; ?>" max="<?php echo $min_max_values['cc_engine']['max']; ?>">
                                <span>-</span>
                                <input type="number" name="max_cc" placeholder="Max" value="<?php echo htmlspecialchars($max_cc_filter); ?>" min="<?php echo $min_max_values['cc_engine']['min']; ?>" max="<?php echo $min_max_values['cc_engine']['max']; ?>">
                            </div>
                        </div>

                        <button type="submit" class="btn-primary filter-submit-btn">Terapkan Filter</button>
                        <a href="search.php" class="btn-secondary filter-reset-btn">Reset Filter</a>
                    </form>
                </aside>

                <div class="search-results-content">
                    <div class="search-bar-container">
                        <form action="search.php" method="GET" class="search-form">
                            <input type="text" name="q" placeholder="Cari kendaraan..." class="search-input" id="searchInput" value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>" autocomplete="off">
                            <button type="submit" class="search-button">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                        <div id="searchResultsDropdown" class="search-suggestions-dropdown"></div>
                    </div>

                    <div class="filter-toggle-container">
                        <button id="filterToggleButton" class="btn-primary filter-toggle-button">
                            <i class="fas fa-filter"></i> Filter Kendaraan
                        </button>
                    </div>


                    <?php
                    if (isset($db_error)) {
                        echo "<p class='error-message msg-info-query'>$db_error</p>";
                    } elseif (empty($vehicles)) {
                        echo "<p  class='no-results msg-info-query'>Tidak ada kendaraan yang ditemukan sesuai kriteria pencarian Anda.</p>";
                    } else { ?>
                        <div class="grid-container">
                            <?php foreach ($vehicles as $vehicle) { ?>
                                <?php render_vehicle_card($vehicle);   ?>
                        <?php } } ?>
                        </div>

                </div>
            </div>
        </section>
    </main>

    <div class="filter-overlay" id="filterOverlay"></div>

    <!-- Chat widget -->
    <?php include("./layouts/chat/chat_widget.php"); ?>

    <!-- Footer -->
    <?php include("./layouts/footer.php"); ?>

    <script src="./js/global.js"></script>
    <script src="./js/script.js"></script>
    <script src="./js/search.js"></script>
</body>

</html>