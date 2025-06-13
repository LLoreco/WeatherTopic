<?php
    require_once __DIR__ . '/../weatherConfig/weatherConfig.php';
    require_once __DIR__ . '/../../logs/logger.php';
    
    class WeatherModel{

        private $logger;
        private $apiKey;
        private $apiUrl;

        public function __construct()
        {
            $this->logger = new Logger();
            $config = require_once __DIR__ . '/../weatherConfig/weatherConfig.php';
            $this->apiKey = $config['apiKey'];
            $this->apiUrl = $config['apiUrl'];
        }

        public function getCurrentWeather($city){
            try{
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
                return ['success' => true, 'message' => 'Weather was get from api'];

            } catch (Exception $e){
                $this->logger->logErrorWeather("Failed in getCurrentWeather: " . $e->getMessage());
                return ['success' => false, 'message' => "Fail to get weather:" . $e->getMessage()];
            }
        }
    }
?>