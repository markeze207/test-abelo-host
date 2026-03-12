<?php
namespace App\Seeders;

use App\Core\Database;
use PDO;
use PDOException;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->command("Создание структуры базы данных...");

        try {
            // Сначала проверяем/создаем базу данных
            $this->createDatabaseIfNotExists();

            // Переподключаемся к созданной БД через Database класс
            $this->db = Database::getInstance()->getConnection();

            // Создаем таблицы (без транзакции, так как некоторые операции могут не поддерживать транзакции)
            $this->createTables();

            $this->command("✓ Структура базы данных успешно создана");
        } catch (\Exception $e) {
            $this->command("✗ Ошибка при создании структуры БД: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Создание базы данных если не существует
     */
    private function createDatabaseIfNotExists(): void
    {
        $this->command("  - Проверка наличия базы данных...");

        // Получаем конфигурацию из Database класса
        $reflection = new \ReflectionClass(Database::class);
        $property = $reflection->getProperty('config');
        $property->setAccessible(true);

        // Создаем временный экземпляр для получения конфига
        $tempInstance = Database::getInstance();
        $config = $property->getValue($tempInstance);

        $dbName = $config['name'];

        // Подключаемся без указания БД
        $dsn = "mysql:host={$config['host']};charset={$config['charset']}";

        try {
            $pdo = new PDO($dsn, $config['user'], $config['pass'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);

            // Создаем базу данных если не существует
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName` 
                        CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

            $this->command("  ✓ База данных '$dbName' проверена/создана");

        } catch (PDOException $e) {
            throw new \Exception("Не удалось создать базу данных: " . $e->getMessage());
        }
    }

    /**
     * Создание всех таблиц
     */
    private function createTables(): void
    {
        $this->command("  - Создание таблиц...");

        // SQL для создания таблиц с оптимизированными индексами
        $sql = "
            CREATE TABLE IF NOT EXISTS categories (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                description TEXT,
                slug VARCHAR(255) UNIQUE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_slug (slug),
                INDEX idx_created_at (created_at),
                INDEX idx_name (name)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE IF NOT EXISTS posts (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                description TEXT,
                content LONGTEXT,
                image VARCHAR(255),
                views INT DEFAULT 0,
                slug VARCHAR(255) UNIQUE,
                published_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_published_at (published_at),
                INDEX idx_views (views),
                INDEX idx_slug (slug),
                INDEX idx_created_at (created_at),
                INDEX idx_views_created (views, created_at),
                INDEX idx_created_views (created_at, views),
                FULLTEXT idx_search (title, description, content)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE IF NOT EXISTS post_category (
                post_id INT,
                category_id INT,
                PRIMARY KEY (post_id, category_id),
                INDEX idx_category_id (category_id),
                INDEX idx_post_id (post_id),
                FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
                FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";

        // Разделяем на отдельные запросы
        $statements = array_filter(
            array_map('trim',
                explode(';', $sql)
            )
        );

        // Выполняем каждый запрос отдельно (без транзакции)
        foreach ($statements as $statement) {
            if (!empty($statement)) {
                try {
                    $this->db->exec($statement);
                } catch (PDOException $e) {
                    // Игнорируем ошибки, если таблицы уже существуют
                    if (strpos($e->getMessage(), 'already exists') === false) {
                        throw new \Exception("Ошибка при создании таблицы: " . $e->getMessage());
                    }
                }
            }
        }

        $this->command("  ✓ Все таблицы созданы");
    }
}