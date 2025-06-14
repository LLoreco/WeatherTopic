<?php
    require_once __DIR__ . '/../weatherConfig/weatherConfig.php';
    require_once __DIR__ . '/../models/weatherModel.php';

    class WeatherController{
        private $weatherModel;

        public function __construct()
        {
            $this->weatherModel = new WeatherModel();
        }

        public function getWeather(){
            header('Content-type: application/json');
            $city = $_GET['city'] ?? 'Moscow';

            $token = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
            if (strpos($token, 'Bearer ') !== 0) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                return;
            }

            $result = $this->weatherModel->getCurrentWeather($city);
            if(!$result['success']){
                http_response_code(400);
            }
            echo json_encode($result);
        }
    }
?>