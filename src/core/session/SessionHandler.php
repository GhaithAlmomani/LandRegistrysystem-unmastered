<?php

namespace MVC\core\session;

use MVC\core\Log;
use SessionHandlerInterface;

class SessionHandler implements SessionHandlerInterface
{
    public function __construct(public string $save_path, public string $prefix)
    {
    }
    public function open(string $path, string $name): bool
    {
        if (!is_dir($this->save_path)) {
            if (!mkdir($this->save_path, 0755, true) && !is_dir($this->save_path)) {
                throw new Log("Failed to create session save directory: $this->save_path");
            }
        }
        return true;
    }
    public function close(): bool
    {
        return true;
    }
    public function read(string $id): string|false
    {
        $file = $this->getSessionFilePath($id);
        if (file_exists($file) && is_readable($file)) {
            $size = filesize($file);
            if ($size > 0) {
                $handle = fopen($file, 'r');
                if ($handle) {
                    flock($handle, LOCK_SH);
                    $data = fread($handle, $size) ?: '';
                    flock($handle, LOCK_UN);
                    fclose($handle);
                    return $data;
                }
            }
        }
        return '';
    }
    public function write(string $id, string $data): bool
    {
        $file = $this->getSessionFilePath($id);
        $handle = fopen($file, 'c');
        if ($handle) {
            flock($handle, LOCK_EX);
            $result = ftruncate($handle, 0) && fwrite($handle, $data) !== false;
            flock($handle, LOCK_UN);
            fclose($handle);
            return $result;
        }
        return false;
    }
    public function destroy(string $id): bool
    {
        $file = $this->getSessionFilePath($id);
        if (file_exists($file)) {
            return unlink($file);
        }
        return true;
    }
    public function gc(int $max_lifetime): int|false
    {
        $files = glob($this->save_path . DIRECTORY_SEPARATOR . $this->prefix . '_*');
        $deleted = 0;

        if ($files !== false) {
            foreach ($files as $file) {
                if (filemtime($file) + $max_lifetime < time() && file_exists($file)) {
                    unlink($file);
                    $deleted++;
                }
            }
        }
        return $deleted;
    }
    private function getSessionFilePath(string $id): string
    {
        $sanitized_id = preg_replace('/[^a-zA-Z0-9]/', '', $id);
        return $this->save_path . DIRECTORY_SEPARATOR . $this->prefix . '_' . $sanitized_id;
    }
}