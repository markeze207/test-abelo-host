<?php
namespace App\Seeders;

use App\Core\QueryBuilder;
use App\Builders\PostBuilder;

class PostSeeder extends Seeder
{
    private array $images = [
        '/uploads/1.avif',
        '/uploads/2.avif',
        '/uploads/3.avif',
    ];

    public function run(): void
    {
        $this->command("Начинаю заполнение постов...");

        // Очищаем таблицы
        $this->truncate('posts');
        $this->truncate('post_category');

        // Получаем ID категорий через QueryBuilder
        $categories = $this->getCategories();

        if (empty($categories)) {
            $this->command("! Нет категорий. Сначала запустите CategorySeeder");
            return;
        }

        // Создаем массив для быстрого доступа к категориям по имени
        $categoryMap = [];
        foreach ($categories as $cat) {
            $categoryMap[$cat['name']] = $cat['id'];
        }

        $posts = [
            // PHP категория - 10 постов
            [
                'title' => 'Введение в PHP 8: новые возможности',
                'description' => 'Обзор ключевых нововведений в PHP 8: атрибуты, union types, match expression и другие',
                'content' => $this->getLongContent('php8'),
                'views' => 1250,
                'image_index' => 1,
                'categories' => ['PHP']
            ],
            [
                'title' => 'Новости PHP: вышел PHP 8.3',
                'description' => 'Обзор новых функций и улучшений в PHP 8.3',
                'content' => $this->getLongContent('php83'),
                'views' => 520,
                'image_index' => 0,
                'categories' => ['PHP', 'Новости']
            ],
            [
                'title' => 'Composer: управление зависимостями в PHP',
                'description' => 'Полное руководство по работе с Composer',
                'content' => $this->getLongContent('composer'),
                'views' => 3400,
                'image_index' => 2,
                'categories' => ['PHP', 'Инструменты']
            ],
            [
                'title' => 'Laravel vs Symfony: сравнение фреймворков',
                'description' => 'Что выбрать для проекта в 2024 году',
                'content' => $this->getLongContent('frameworks'),
                'views' => 5600,
                'image_index' => 0,
                'categories' => ['PHP']
            ],
            [
                'title' => 'PSR стандарты в PHP',
                'description' => 'Что такое PSR и почему их важно соблюдать',
                'content' => $this->getLongContent('psr'),
                'views' => 2100,
                'image_index' => 0,
                'categories' => ['PHP']
            ],
            [
                'title' => 'ООП в PHP: продвинутые техники',
                'description' => 'Трейты, интерфейсы, абстрактные классы',
                'content' => $this->getLongContent('oop'),
                'views' => 4300,
                'image_index' => 0,
                'categories' => ['PHP']
            ],
            [
                'title' => 'Тестирование в PHP: PHPUnit и Pest',
                'description' => 'Как писать тесты и почему это важно',
                'content' => $this->getLongContent('testing'),
                'views' => 1850,
                'image_index' => 2,
                'categories' => ['PHP']
            ],
            [
                'title' => 'Асинхронность в PHP: ReactPHP и Swoole',
                'description' => 'Возможности асинхронного программирования',
                'content' => $this->getLongContent('async'),
                'views' => 980,
                'image_index' => 1,
                'categories' => ['PHP']
            ],
            [
                'title' => 'PHP и MySQL: работа с базами данных',
                'description' => 'PDO, prepared statements, транзакции',
                'content' => $this->getLongContent('php-mysql'),
                'views' => 6700,
                'image_index' => 0,
                'categories' => ['PHP', 'Базы данных']
            ],
            [
                'title' => 'Безопасность PHP-приложений',
                'description' => 'Защита от XSS, CSRF, SQL-инъекций',
                'content' => $this->getLongContent('php-security'),
                'views' => 3900,
                'image_index' => 2,
                'categories' => ['PHP', 'Безопасность']
            ],

            // Остальные категории
            [
                'title' => 'Современный JavaScript: что нужно знать в 2024',
                'description' => 'Обзор актуальных возможностей ES6+ и популярных фреймворков',
                'content' => $this->getLongContent('js2024'),
                'views' => 980,
                'image_index' => 2,
                'categories' => ['JavaScript']
            ],
            [
                'title' => 'TypeScript: почему стоит использовать',
                'description' => 'Преимущества типизации в JavaScript проектах',
                'content' => $this->getLongContent('typescript'),
                'views' => 890,
                'image_index' => 1,
                'categories' => ['JavaScript', 'Frontend']
            ],
            [
                'title' => 'Современный CSS: Grid и Flexbox',
                'description' => 'Полное руководство по современным методам верстки',
                'content' => $this->getLongContent('css'),
                'views' => 1500,
                'image_index' => 0,
                'categories' => ['Frontend']
            ],
            [
                'title' => 'Оптимизация запросов MySQL',
                'description' => 'Практические советы по ускорению работы с базами данных',
                'content' => $this->getLongContent('mysql'),
                'views' => 750,
                'image_index' => 0,
                'categories' => ['Базы данных']
            ],
            [
                'title' => 'PostgreSQL vs MySQL: что выбрать?',
                'description' => 'Сравнение двух популярных СУБД',
                'content' => $this->getLongContent('postgresql'),
                'views' => 620,
                'image_index' => 2,
                'categories' => ['Базы данных']
            ],
            [
                'title' => 'Docker для разработчика: основы',
                'description' => 'Как начать использовать Docker в повседневной разработке',
                'content' => $this->getLongContent('docker'),
                'views' => 2100,
                'image_index' => 0,
                'categories' => ['Инструменты', 'DevOps']
            ],
            [
                'title' => 'Git: продвинутые техники',
                'description' => 'Rebase, cherry-pick и другие полезные команды',
                'content' => $this->getLongContent('git'),
                'views' => 1450,
                'image_index' => 1,
                'categories' => ['Инструменты']
            ],
            [
                'title' => 'Как успешно пройти собеседование в IT',
                'description' => 'Советы по подготовке к техническим интервью',
                'content' => $this->getLongContent('interview'),
                'views' => 3500,
                'image_index' => 2,
                'categories' => ['Карьера']
            ],
            [
                'title' => 'Python для начинающих: с чего начать',
                'description' => 'Введение в Python для новичков',
                'content' => $this->getLongContent('python'),
                'views' => 1800,
                'image_index' => 2,
                'categories' => ['Python']
            ],
            [
                'title' => 'Django vs Flask: сравнение фреймворков',
                'description' => 'Что выбрать для веб-разработки на Python',
                'content' => $this->getLongContent('django'),
                'views' => 950,
                'image_index' => 1,
                'categories' => ['Python']
            ],
            [
                'title' => 'CI/CD с GitHub Actions',
                'description' => 'Настройка непрерывной интеграции и доставки',
                'content' => $this->getLongContent('cicd'),
                'views' => 780,
                'image_index' => 0,
                'categories' => ['DevOps']
            ],
            [
                'title' => 'Kubernetes для начинающих',
                'description' => 'Основы оркестрации контейнеров',
                'content' => $this->getLongContent('k8s'),
                'views' => 630,
                'image_index' => 0,
                'categories' => ['DevOps']
            ],
            [
                'title' => 'Алгоритмы сортировки: подробный разбор',
                'description' => 'Пузырьковая, быстрая, сортировка слиянием',
                'content' => $this->getLongContent('algorithms'),
                'views' => 2100,
                'image_index' => 2,
                'categories' => ['Алгоритмы и структуры данных']
            ],
            [
                'title' => 'Введение в Go: особенности языка',
                'description' => 'Почему Go становится популярным',
                'content' => $this->getLongContent('go'),
                'views' => 890,
                'image_index' => 2,
                'categories' => ['Go']
            ],
            [
                'title' => 'React Native vs Flutter: что выбрать',
                'description' => 'Сравнение кроссплатформенных фреймворков',
                'content' => $this->getLongContent('mobile'),
                'views' => 1200,
                'image_index' => 0,
                'categories' => ['Мобильная разработка', 'JavaScript']
            ],
            [
                'title' => 'Основы безопасности веб-приложений',
                'description' => 'Защита от XSS, CSRF, SQL-инъекций',
                'content' => $this->getLongContent('security'),
                'views' => 1100,
                'image_index' => 1,
                'categories' => ['Безопасность']
            ],
            [
                'title' => 'Введение в машинное обучение',
                'description' => 'Основные концепции и алгоритмы ML',
                'content' => $this->getLongContent('ml'),
                'views' => 2300,
                'image_index' => 1,
                'categories' => ['Искусственный интеллект']
            ],
            [
                'title' => 'Топ-10 книг для программиста',
                'description' => 'Лучшие книги по программированию',
                'content' => $this->getLongContent('books'),
                'views' => 3400,
                'image_index' => 0,
                'categories' => ['Книги и ресурсы']
            ],
            [
                'title' => 'Архив: старая статья про PHP 7',
                'description' => 'Устаревшая, но всё ещё полезная информация',
                'content' => 'Когда-то это было актуально...',
                'views' => 120,
                'image_index' => 0,
                'categories' => ['Архив']
            ]
        ];

        $postQueryBuilder = new QueryBuilder('posts');
        $relationQueryBuilder = new QueryBuilder('post_category');

        $inserted = 0;
        $relations = 0;

        foreach ($posts as $postData) {
            try {
                // Случайная дата публикации (последние 30 дней)
                $publishedAt = date('Y-m-d H:i:s', strtotime('-' . rand(1, 30) . ' days'));

                // Создаем данные для поста
                $data = [
                    'title' => $postData['title'],
                    'description' => $postData['description'],
                    'content' => $postData['content'],
                    'views' => $postData['views'],
                    'image' => $this->images[$postData['image_index']],
                    'created_at' => $publishedAt,
                    'updated_at' => $publishedAt
                ];

                // Добавляем slug из названия (транслитерация)
                $data['slug'] = $this->createSlug($postData['title']);

                // Вставляем пост через QueryBuilder
                $postId = $postQueryBuilder->insert($data);

                if (!$postId) {
                    throw new \Exception("Не удалось создать пост");
                }

                // Привязываем категории
                foreach ($postData['categories'] as $categoryName) {
                    if (isset($categoryMap[$categoryName])) {
                        $relationQueryBuilder->insert([
                            'post_id' => $postId,
                            'category_id' => $categoryMap[$categoryName]
                        ]);
                        $relations++;
                    } else {
                        $this->command("  ! Категория '{$categoryName}' не найдена для поста '{$postData['title']}'");
                    }
                }

                $inserted++;
                $this->command("  + Пост '{$data['title']}' создан (ID: $postId)");

            } catch (\Exception $e) {
                $this->command("  ! Ошибка при создании поста '{$postData['title']}': " . $e->getMessage());
            }
        }

        $this->command("✓ Заполнение постов завершено. Добавлено: $inserted, связей: $relations");
    }

    /**
     * Создать slug из заголовка (простая транслитерация)
     */
    private function createSlug(string $title): string
    {
        $converter = [
            'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd',
            'е' => 'e', 'ё' => 'e', 'ж' => 'zh', 'з' => 'z', 'и' => 'i',
            'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n',
            'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't',
            'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => 'ch',
            'ш' => 'sh', 'щ' => 'sch', 'ь' => '', 'ы' => 'y', 'ъ' => '',
            'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
        ];

        $slug = mb_strtolower($title);
        $slug = strtr($slug, $converter);
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        $slug = trim($slug, '-');

        return $slug;
    }

    /**
     * Получить все категории из БД через QueryBuilder
     */
    private function getCategories(): array
    {
        $queryBuilder = new QueryBuilder('categories');
        return $queryBuilder
            ->select(['id', 'name'])
            ->orderBy('id')
            ->get();
    }

    /**
     * Получить длинный контент для поста
     */
    private function getLongContent(string $type): string
    {
        $contents = [
            'php8' => '<p>PHP 8 привнес множество значительных улучшений в язык. Рассмотрим ключевые нововведения:</p>
            
            <h2>Атрибуты (Attributes)</h2>
            <p>Атрибуты заменяют dockblock-аннотации и предоставляют структурированный способ добавления метаданных.</p>
            
            <h2>Union Types</h2>
            <p>Теперь можно указывать несколько возможных типов для одного параметра или возвращаемого значения.</p>
            
            <h2>Match Expression</h2>
            <p>Более мощная альтернатива switch с возвратом значения.</p>
            
            <h2>Named Arguments</h2>
            <p>Позволяют передавать аргументы по имени, а не по позиции.</p>
            
            <p>Эти и многие другие улучшения делают PHP 8 отличным выбором для современных проектов.</p>',

            'php83' => '<p>PHP 8.3 принес множество улучшений:</p>
            
            <h2>Новые функции</h2>
            <p>Улучшения в работе с массивами, новые методы классов.</p>
            
            <h2>Производительность</h2>
            <p>Оптимизация JIT и других компонентов.</p>',

            'composer' => '<p>Composer - менеджер зависимостей для PHP.</p>
            
            <h2>Установка и настройка</h2>
            <p>Как установить Composer и настроить autoload.</p>
            
            <h2>composer.json</h2>
            <p>Структура файла, версионирование зависимостей.</p>
            
            <h2>Создание пакетов</h2>
            <p>Как создать и опубликовать свой пакет на Packagist.</p>',

            'frameworks' => '<p>Laravel и Symfony - два самых популярных PHP фреймворка.</p>
            
            <h2>Laravel</h2>
            <p>Простота использования, богатая экосистема, Eloquent ORM.</p>
            
            <h2>Symfony</h2>
            <p>Компонентная архитектура, гибкость, производительность.</p>
            
            <h2>Что выбрать?</h2>
            <p>Сравнение по ключевым критериям для вашего проекта.</p>',

            'psr' => '<p>PSR (PHP Standards Recommendations) - стандарты PHP.</p>
            
            <h2>PSR-1 и PSR-2</h2>
            <p>Базовые стандарты кодирования.</p>
            
            <h2>PSR-4</h2>
            <p>Autoloading стандарт.</p>
            
            <h2>PSR-7</h2>
            <p>HTTP message interfaces.</p>
            
            <h2>PSR-12</h2>
            <p>Расширенный стиль кодирования.</p>',

            'oop' => '<p>Объектно-ориентированное программирование в PHP.</p>
            
            <h2>Наследование</h2>
            <p>Родительские и дочерние классы.</p>
            
            <h2>Интерфейсы</h2>
            <p>Контракты для классов.</p>
            
            <h2>Трейты</h2>
            <p>Горизонтальное переиспользование кода.</p>
            
            <h2>Абстрактные классы</h2>
            <p>Базовые классы с частичной реализацией.</p>',

            'testing' => '<p>Тестирование - важная часть разработки.</p>
            
            <h2>PHPUnit</h2>
            <p>Классический фреймворк для тестирования.</p>
            
            <h2>Pest</h2>
            <p>Современный, элегантный синтаксис.</p>
            
            <h2>Моки и стабы</h2>
            <p>Изоляция тестируемого кода.</p>
            
            <h2>TDD</h2>
            <p>Разработка через тестирование.</p>',

            'async' => '<p>Асинхронное программирование в PHP.</p>
            
            <h2>ReactPHP</h2>
            <p>Событийно-ориентированное программирование.</p>
            
            <h2>Swoole</h2>
            <p>Высокопроизводительный сервер для PHP.</p>
            
            <h2>Когда нужно?</h2>
            <p>Real-time приложения, веб-сокеты.</p>',

            'php-mysql' => '<p>Работа с MySQL в PHP.</p>
            
            <h2>PDO</h2>
            <p>Универсальный интерфейс для работы с БД.</p>
            
            <h2>Prepared Statements</h2>
            <p>Защита от SQL-инъекций.</p>
            
            <h2>Транзакции</h2>
            <p>Группировка операций.</p>',

            'php-security' => '<p>Безопасность PHP приложений.</p>
            
            <h2>XSS</h2>
            <p>Защита от межсайтового скриптинга.</p>
            
            <h2>CSRF</h2>
            <p>Защита от подделки межсайтовых запросов.</p>
            
            <h2>SQL Injection</h2>
            <p>Предотвращение SQL-инъекций.</p>
            
            <h2>Хеширование паролей</h2>
            <p>password_hash() и password_verify().</p>',

            'js2024' => '<p>JavaScript продолжает активно развиваться. Вот что важно знать в 2024 году:</p>
            
            <h2>ES2023 и ES2024</h2>
            <p>Новые методы массивов, улучшения работы с промисами.</p>
            
            <h2>Фреймворки</h2>
            <p>React, Vue и Angular остаются основными игроками, но растет популярность Solid и Svelte.</p>
            
            <h2>TypeScript</h2>
            <p>Стал стандартом для крупных проектов.</p>',

            'typescript' => '<p>TypeScript добавляет статическую типизацию в JavaScript.</p>
            
            <h2>Преимущества</h2>
            <p>Раннее обнаружение ошибок, лучшая поддержка IDE, самодокументируемый код.</p>
            
            <h2>Интеграция</h2>
            <p>Как начать использовать TypeScript в существующем проекте.</p>',

            'css' => '<p>Современный CSS предлагает мощные инструменты для верстки.</p>
            
            <h2>Grid</h2>
            <p>Двухмерная система раскладки.</p>
            
            <h2>Flexbox</h2>
            <p>Одномерная раскладка для компонентов.</p>',

            'mysql' => '<p>Оптимизация запросов критически важна для производительности приложений.</p>
            
            <h2>Индексы</h2>
            <p>Правильное использование индексов может ускорить запросы в сотни раз.</p>
            
            <h2>EXPLAIN</h2>
            <p>Анализ плана выполнения запросов помогает найти узкие места.</p>',

            'postgresql' => '<p>PostgreSQL предлагает множество возможностей, которых нет в MySQL.</p>
            
            <h2>JSONB</h2>
            <p>Эффективная работа с JSON данными.</p>
            
            <h2>Полнотекстовый поиск</h2>
            <p>Встроенные возможности поиска по тексту.</p>',

            'docker' => '<p>Docker изменил подход к разработке и деплою приложений.</p>
            
            <h2>Основные понятия</h2>
            <p>Контейнеры, образы, Dockerfile, docker-compose.</p>
            
            <h2>Практическое применение</h2>
            <p>Как организовать среду разработки с несколькими сервисами.</p>',

            'git' => '<p>Освоив продвинутые техники Git, вы сможете эффективнее управлять историей проекта.</p>
            
            <h2>Rebase</h2>
            <p>Перемещение и объединение коммитов для чистой истории.</p>
            
            <h2>Cherry-pick</h2>
            <p>Выборочное применение коммитов из других веток.</p>',

            'interview' => '<p>Подготовка к собеседованию требует системного подхода.</p>
            
            <h2>Техническая часть</h2>
            <p>Алгоритмы, структуры данных, особенности языка.</p>
            
            <h2>Soft skills</h2>
            <p>Коммуникация, работа в команде, решение конфликтов.</p>',

            'python' => '<p>Python - один из самых популярных языков программирования.</p>
            
            <h2>Простота изучения</h2>
            <p>Чистый синтаксис и большое сообщество.</p>
            
            <h2>Области применения</h2>
            <p>Веб-разработка, анализ данных, автоматизация.</p>',

            'django' => '<p>Django - мощный веб-фреймворк для Python.</p>
            
            <h2>Преимущества</h2>
            <p>Встроенная админка, ORM, безопасность.</p>',

            'cicd' => '<p>CI/CD автоматизирует процесс разработки и доставки.</p>
            
            <h2>GitHub Actions</h2>
            <p>Простая настройка pipeline прямо в репозитории.</p>',

            'k8s' => '<p>Kubernetes - стандарт оркестрации контейнеров.</p>
            
            <h2>Основные понятия</h2>
            <p>Pods, services, deployments, ingress.</p>',

            'algorithms' => '<p>Понимание алгоритмов критически важно для программиста.</p>
            
            <h2>Сортировка</h2>
            <p>Различные алгоритмы и их сложность.</p>',

            'go' => '<p>Go сочетает производительность и простоту.</p>
            
            <h2>Горутины</h2>
            <p>Легковесные потоки для конкурентности.</p>',

            'mobile' => '<p>Кроссплатформенная разработка экономит ресурсы.</p>
            
            <h2>React Native</h2>
            <p>JavaScript, большой экосистема.</p>
            
            <h2>Flutter</h2>
            <p>Dart, отличная производительность.</p>',

            'security' => '<p>Безопасность должна быть встроена в процесс разработки.</p>
            
            <h2>Основные уязвимости</h2>
            <p>XSS, CSRF, SQLi, их предотвращение.</p>',

            'ml' => '<p>Машинное обучение меняет мир.</p>
            
            <h2>Основные концепции</h2>
            <p>Обучение с учителем и без, нейросети.</p>',

            'books' => '<p>Книги помогают углубить знания.</p>
            
            <h2>Классика</h2>
            <p>"Совершенный код", "Чистый код", "Программист-прагматик".</p>'
        ];

        return $contents[$type] ?? '<p>Содержимое поста</p>';
    }
}