<?php
require_once __DIR__ . '/../weatherConfig/weatherConfig.php';
require_once __DIR__ . '/../database/database.php';
require_once __DIR__ . '/../../logs/logger.php';

class WeatherModel {
    private $logger;
    private $apiKey;
    private $apiUrl;

    public function __construct() {
        $this->logger = new Logger();
        $configFilePath = __DIR__ . '/../weatherConfig/weatherConfig.php';
        $config = null;
        try {
            $config = require $configFilePath;
        } catch (Exception $e) {
            $this->logger->logErrorWeather("Error loading config file: " . $e->getMessage());
            throw new Exception("Failed to load configuration file");
        }

        $this->apiKey = $config['apiKey'] ?? null;
        $this->apiUrl = $config['apiUrl'] ?? null;
        $this->logger->logErrorWeather("Extracted apiKey: " . var_export($this->apiKey, true) . ", apiUrl: " . var_export($this->apiUrl, true));
        if (!$this->apiKey || !$this->apiUrl) {
            $this->logger->logErrorWeather("Missing apiKey or apiUrl in config");
            throw new Exception("API configuration missing");
        }
    }

    public function getCurrentWeather($city) {
        try {
            $url = "{$this->apiUrl}?key={$this->apiKey}&q={$city}&aqi=no";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);

            if (curl_errno($ch)) {
                $this->logger->logErrorWeather("cURL error in getCurrentWeather: " . curl_error($ch));
                return ['success' => false, 'message' => 'Ошибка подключения к API'];
            }

            curl_close($ch);
            $data = json_decode($response, true);

            if (isset($data['error'])) {
                $this->logger->logErrorWeather("API error in getCurrentWeather: " . $data['error']['message']);
                return ['success' => false, 'message' => $data['error']['message']];
            }

            $weatherData = [
                'city' => $data['location']['name'],
                'temperature' => $data['current']['temp_c'],
                'description' => $data['current']['condition']['text'],
                'icon' => $data['current']['condition']['icon'],
                'humidity' => $data['current']['humidity'],
                'windSpeed' => $data['current']['wind_mph'] * 0.44
            ];

            return ['success' => true, 'data' => $weatherData];
        } catch (Exception $e) {
            $this->logger->logErrorWeather("Failed in getCurrentWeather: " . $e->getMessage());
            return ['success' => false, 'message' => "Fail to get weather: " . $e->getMessage()];
        }
    }
}
?>