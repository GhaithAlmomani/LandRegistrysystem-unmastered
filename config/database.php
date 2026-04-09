<?php

/**
 * Centralized Database Connection & Environment Configuration
 * 
 * All credentials are loaded from the .env file in the project root.
 * This replaces all hardcoded PDO connection blocks across the codebase.
 * 
 * Usage:
 *   require_once __DIR__ . '/../config/database.php';  // adjust path as needed
 *   $con = Database::getConnection();
 *   $apiKey = Database::getEnv('ETHERSCAN_API_KEY');
 */

class Database
{
    private static ?PDO $pdo = null;
    private static array $env = [];
    private static bool $envLoaded = false;

    /**
     * Parse the .env file from the project root (one time only).
     */
    private static function loadEnv(): void
    {
        if (self::$envLoaded) {
            return;
        }

        $envPath = __DIR__ . '/../.env';

        if (!file_exists($envPath)) {
            throw new RuntimeException(
                '.env file not found. Copy .env.example to .env and fill in your credentials.'
            );
        }

        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            // Skip comments
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            $parts = explode('=', $line, 2);
            if (count($parts) === 2) {
                $key = trim($parts[0]);
                $value = trim($parts[1]);
                self::$env[$key] = $value;
            }
        }

        self::$envLoaded = true;
    }

    /**
     * Get a single PDO connection (singleton — reused for the entire request).
     */
    public static function getConnection(): PDO
    {
        if (self::$pdo !== null) {
            return self::$pdo;
        }

        self::loadEnv();

        $host   = self::$env['DB_HOST']   ?? '127.0.0.1';
        $dbname = self::$env['DB_NAME']   ?? 'wise';
        $user   = self::$env['DB_USER']   ?? 'root';
        $pass   = self::$env['DB_PASS']   ?? '';

        $dsn = "mysql:host={$host};dbname={$dbname}";
        $options = [
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        ];

        self::$pdo = new PDO($dsn, $user, $pass, $options);

        return self::$pdo;
    }

    /**
     * Read any key from the .env file.
     *
     * @param string $key     The environment variable name
     * @param string $default Fallback if the key is missing
     */
    public static function getEnv(string $key, string $default = ''): string
    {
        self::loadEnv();
        return self::$env[$key] ?? $default;
    }
}
