<?php
    header('Access-Control-Allow-Origin: http://localhost:3000');
    header('Access-Control-Allow-Methods: POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit;
    }

    require_once __DIR__ . '/../controllers/weatherController.php';

    $weatherController = new WeatherController();

    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['city'])) {
        $weatherController->getWeather();
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid request. Please provide a city parameter.']);
    }
?>