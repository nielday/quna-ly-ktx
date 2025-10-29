<?php
require_once __DIR__ . '/database.php';

class Logger {
    private $logPath;
    private $logLevel;
    
    public function __construct() {
        $this->logPath = LOG_PATH;
        $this->logLevel = LOG_LEVEL;
        
        // Tạo thư mục logs nếu chưa có
        if (!is_dir($this->logPath)) {
            mkdir($this->logPath, 0755, true);
        }
    }
    
    public function log($level, $message, $context = []) {
        $levels = ['DEBUG' => 0, 'INFO' => 1, 'WARNING' => 2, 'ERROR' => 3];
        $currentLevel = $levels[$this->logLevel] ?? 1;
        $messageLevel = $levels[$level] ?? 1;
        
        if ($messageLevel >= $currentLevel) {
            $timestamp = date('Y-m-d H:i:s');
            $logMessage = "[$timestamp] [$level] $message";
            
            if (!empty($context)) {
                $logMessage .= " | Context: " . json_encode($context);
            }
            
            $logFile = $this->logPath . 'app_' . date('Y-m-d') . '.log';
            file_put_contents($logFile, $logMessage . PHP_EOL, FILE_APPEND | LOCK_EX);
        }
    }
    
    public function debug($message, $context = []) {
        $this->log('DEBUG', $message, $context);
    }
    
    public function info($message, $context = []) {
        $this->log('INFO', $message, $context);
    }
    
    public function warning($message, $context = []) {
        $this->log('WARNING', $message, $context);
    }
    
    public function error($message, $context = []) {
        $this->log('ERROR', $message, $context);
    }
}
?>
