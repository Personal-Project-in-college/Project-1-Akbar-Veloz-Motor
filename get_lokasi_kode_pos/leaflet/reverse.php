<?php
header('Content-Type: application/json');

if (!isset($_GET['lat']) || !isset($_GET['lon'])) {
    echo json_encode(['error' => 'Latitude dan longitude dibutuhkan']);
    exit;
}

$lat = $_GET['lat'];
$lon = $_GET['lon'];

$url = "https://nominatim.openstreetmap.org/reverse?lat=$lat&lon=$lon&format=json";

$options = [
    "http" => [
        "header" => "User-Agent: PHP-Geolocation-App"
    ]
];

$context = stream_context_create($options);
$response = file_get_contents($url, false, $context);
$data = json_decode($response, true);

$address = $data['address'] ?? [];

echo json_encode([
    'kecamatan' => $address['suburb'] ?? $address['village'] ?? $address['county'] ?? null,
    'kodepos' => $address['postcode'] ?? null
]);
