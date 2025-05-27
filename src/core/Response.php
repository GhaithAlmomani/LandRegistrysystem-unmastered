<?php

namespace MVC\core;

class Response
{
    public function setStatusCode(int $code): void {
        http_response_code($code);
    }
    public function json($data): void {
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function setSecurityHeaders(): void {
        // Prevent clickjacking
        header('X-Frame-Options: DENY');
        
        // Enable XSS protection
        header('X-XSS-Protection: 1; mode=block');
        
        // Prevent MIME type sniffing
        header('X-Content-Type-Options: nosniff');
        
        // Strict Transport Security
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
        
        // Content Security Policy
        header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline'; img-src 'self' data:; font-src 'self' data:;");
        
        // Referrer Policy
        header('Referrer-Policy: strict-origin-when-cross-origin');
        
        // Permissions Policy
        header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
    }
}