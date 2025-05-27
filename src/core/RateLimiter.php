<?php

namespace MVC\core;

class RateLimiter
{
    private const string CONFIG_RATE_LIMIT_PATH = 'MVC.rate_limit_path';
    private const int DEFAULT_MAX_ATTEMPTS = 5;
    private const int DEFAULT_WINDOW = 300; // 5 minutes in seconds

    public static function check(string $key, int $maxAttempts = self::DEFAULT_MAX_ATTEMPTS, int $window = self::DEFAULT_WINDOW): bool
    {
        $path = config(self::CONFIG_RATE_LIMIT_PATH);
        $file = $path . md5($key) . '.limiter';
        
        if (!file_exists($file)) {
            self::resetAttempts($file);
            return true;
        }

        $data = json_decode(file_get_contents($file), true);
        if (!$data) {
            self::resetAttempts($file);
            return true;
        }

        if (time() - $data['timestamp'] > $window) {
            self::resetAttempts($file);
            return true;
        }

        if ($data['attempts'] >= $maxAttempts) {
            return false;
        }

        $data['attempts']++;
        file_put_contents($file, json_encode($data));
        return true;
    }

    private static function resetAttempts(string $file): void
    {
        $data = [
            'attempts' => 1,
            'timestamp' => time()
        ];
        file_put_contents($file, json_encode($data));
    }

    public static function getRemainingAttempts(string $key): int
    {
        $path = config(self::CONFIG_RATE_LIMIT_PATH);
        $file = $path . md5($key) . '.limiter';
        
        if (!file_exists($file)) {
            return self::DEFAULT_MAX_ATTEMPTS;
        }

        $data = json_decode(file_get_contents($file), true);
        if (!$data) {
            return self::DEFAULT_MAX_ATTEMPTS;
        }

        if (time() - $data['timestamp'] > self::DEFAULT_WINDOW) {
            return self::DEFAULT_MAX_ATTEMPTS;
        }

        return max(0, self::DEFAULT_MAX_ATTEMPTS - $data['attempts']);
    }
} 