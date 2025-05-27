<?php

namespace MVC\core;

use LogicException;
use RuntimeException;

class Log extends LogicException
{
    protected string $log_file;
    public const string DEFAULT_LOG_FILE = 'error.log';
    private const string CONFIG_LOG_PATH = 'MVC.log_path';
    public function __construct(string $message, int $code = 0, ?LogicException $previous = null, string $log_file = self::DEFAULT_LOG_FILE)
    {
        parent::__construct($message,$code,$previous);
        $this->log_file = $log_file;
        $this->logError();
    }
    public function logError(): void
    {
        $remoteAddr = $_SERVER['REMOTE_ADDR'] ?? 'unknown IP';
        $requestUri = filter_var($_SERVER['REQUEST_URI'] ?? '', FILTER_SANITIZE_URL);
        $logMessage = "$remoteAddr - " . date('Y-m-d H:i:s') . " - Error {$this->getMessage()} in {$this->getFile()} on line {$this->getLine()} $requestUri\n";

        if (file_put_contents(config(self::CONFIG_LOG_PATH) . $this->log_file, $logMessage, FILE_APPEND) === false) {
            throw new RuntimeException("Failed to write log");
        }
    }
}