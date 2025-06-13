<?php
    require_once __DIR__ . '/../weatherConfig/weatherConfig.php';
    require_once __DIR__ . '/../../logs/logger.php';

    class WeatherController{
        private $logger;

        private function __construct()
        {
            $this->logger = new Logger();
        }
    }
?>