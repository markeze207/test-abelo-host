<?php
header('Content-Type: text/html; charset=utf-8');

require_once __DIR__ . '/../vendor/autoload.php';

// Загрузка конфигурации
$config = require __DIR__ . '/../config/database.php';
define('ENVIRONMENT', $config['environment'] ?? 'production');

require_once __DIR__ . '/../web/routes.php';