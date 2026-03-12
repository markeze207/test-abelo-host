<?php
namespace App\Seeders;

use App\Core\Database;
use PDO;

abstract class Seeder
{
    protected PDO $db;

    public function __construct()
    {
        try {
            $this->db = Database::getInstance()->getConnection();
        } catch (\Exception $e) {
            // Если БД еще не существует, это нормально для DatabaseSeeder
            // Он создаст ее позже
            if (get_class($this) !== DatabaseSeeder::class) {
                throw $e;
            }
        }
    }

    /**
     * Запустить сидер
     */
    abstract public function run(): void;

    /**
     * Вывести сообщение в консоль
     */
    protected function command(string $message): void
    {
        if (PHP_SAPI === 'cli') {
            echo $message . PHP_EOL;
        }
    }

    /**
     * Очистить таблицу
     */
    protected function truncate(string $table): void
    {
        $this->db->exec("SET FOREIGN_KEY_CHECKS = 0");
        $this->db->exec("TRUNCATE TABLE `$table`");
        $this->db->exec("SET FOREIGN_KEY_CHECKS = 1");
        $this->command("✓ Таблица '$table' очищена");
    }

    /**
     * Начать транзакцию
     */
    protected function beginTransaction(): void
    {
        $this->db->beginTransaction();
    }

    /**
     * Завершить транзакцию
     */
    protected function commit(): void
    {
        $this->db->commit();
    }

    /**
     * Откатить транзакцию
     */
    protected function rollback(): void
    {
        $this->db->rollBack();
    }

    /**
     * Запустить все сидеры
     */
    public static function runAll(): void
    {
        echo "Запуск сидеров...\n\n";

        $seeders = [
            DatabaseSeeder::class,  // Сначала создаем структуру БД
            CategorySeeder::class,  // Затем категории
            PostSeeder::class,      // Затем посты
        ];

        foreach ($seeders as $seederClass) {
            $shortName = basename(str_replace('\\', '/', $seederClass));
            echo "→ {$shortName}\n";

            try {
                /** @var Seeder $seeder */
                $seeder = new $seederClass();
                $seeder->run();
                echo "\n";
            } catch (\Exception $e) {
                echo "  ✗ Ошибка: " . $e->getMessage() . "\n\n";
                throw $e;
            }
        }

        echo "Готово!\n";
    }
}