<?php
require __DIR__ . '/vendor/autoload.php';

use App\Seeders\Seeder;

try {
    Seeder::runAll();
} catch (Exception $e) {
    die("Ошибка: " . $e->getMessage() . "\n");
}