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
        $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown Browser';
        $referer = $_SERVER['HTTP_REFERER'] ?? 'No Referer';
        $sessionId = session_id() ?? 'No Session';
        $username = $_SESSION['Username'] ?? 'Not Logged In';
        $userRole = $_SESSION['role'] ?? 'No Role';

        $logMessage = sprintf(
            "[%s] %s\n" .
            "IP: %s\n" .
            "Request: %s %s\n" .
            "User Agent: %s\n" .
            "Referer: %s\n" .
            "Session ID: %s\n" .
            "User: %s (Role: %s)\n" .
            "Error: %s\n" .
            "File: %s\n" .
            "Line: %d\n" .
            "Stack Trace:\n%s\n" .
            "----------------------------------------\n",
            date('Y-m-d H:i:s'),
            $this->getMessage(),
            $remoteAddr,
            $requestMethod,
            $requestUri,
            $userAgent,
            $referer,
            $sessionId,
            $username,
            $userRole,
            $this->getMessage(),
            $this->getFile(),
            $this->getLine(),
            $this->getTraceAsString()
        );

        if (file_put_contents(config(self::CONFIG_LOG_PATH) . $this->log_file, $logMessage, FILE_APPEND) === false) {
            throw new RuntimeException("Failed to write log");
        }
    }
}