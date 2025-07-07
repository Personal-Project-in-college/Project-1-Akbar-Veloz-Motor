<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed.']);
    exit();
}

$lat = $_GET['lat'] ?? null;
$lon = $_GET['lon'] ?? null;
$query = $_GET['q'] ?? null;
$type = $_GET['type'] ?? 'reverse';

if ($type === 'reverse' && (!isset($lat) || !isset($lon))) {
    http_response_code(400);
    echo json_encode(['error' => 'Latitude and Longitude are required for reverse geocoding.']);
    exit();
}

if ($type === 'search' && !isset($query)) {
    http_response_code(400);
    echo json_encode(['error' => 'Query is required for search geocoding.']);
    exit();
}

$nominatim_url = '';
if ($type === 'reverse') {
    $nominatim_url = "https://nominatim.openstreetmap.org/reverse?lat={$lat}&lon={$lon}&format=json&addressdetails=1";
} else if ($type === 'search') {
    $nominatim_url = "https://nominatim.openstreetmap.org/search?q={$query}&format=json&addressdetails=1&limit=1";
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request type.']);
    exit();
}

$options = [
    'http' => [
        'header' => "User-Agent: AkbarVelozMotorApp/1.0 (admin@akbarvelozmotor.com)\r\n"
    ]
];
$context = stream_context_create($options);

$response = @file_get_contents($nominatim_url, false, $context);

if ($response === FALSE) {
    $error_details = error_get_last();
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch data from Nominatim.', 'details' => $error_details['message'] ?? 'Unknown error.']);
} else {
    echo $response;
}
?>