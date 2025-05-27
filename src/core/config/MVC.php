<?php
return [
    'log_path'  => APP_PATH . '..' . DS . 'storage' . DS . 'log' . DS,
    'storageP'  => APP_PATH . '..' . DS . 'storage' . DS . 'public',
    'route'     => APP_PATH . 'core' . DS . 'routes' . DS,
    'view'      => APP_PATH . 'view' . DS,
    'name'      => 'Wise',
    'version'   => 'v1.0',
    //Session
    'session_path'      => APP_PATH . '..' . DS . 'storage' . DS . 'session',
    'session_prefix'    =>'MVCApp',
    'session_timeout'   => 86400,
    'session_driver'    =>'file',
    //Hash
    'encryption_mode'   => 'AES-256-CBC',
    'encryption_key'    => bin2hex(random_bytes(32)),
    'bcrypt_algo'       => PASSWORD_BCRYPT,
    // Rate Limiting
    'rate_limit_path'   => APP_PATH . '..' . DS . 'storage' . DS . 'rate_limits' . DS,
    // Security
    'allowed_origins'   => ['https://yourdomain.com'],
    'cors_headers'      => true,
    'secure_cookies'    => true,
    'session_secure'    => true,
    'session_httponly'  => true,
    'session_samesite'  => 'Lax',
];