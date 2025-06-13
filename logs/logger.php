<?php
class Logger{
    private $logFileUser = __DIR__ . '/../logs/user_errors.log';
    private $logFileWeather = __DIR__ . '/../logs/weather_errors.log';

    public function logErrorUser($message) {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] $message" . PHP_EOL;
        error_log($logMessage, 3, $this->logFileUser);
    }
    
    public function logErrorWeather($message) {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] $message" . PHP_EOL;
        error_log($logMessage, 3, $this->logFileWeather);
    }
}
?>