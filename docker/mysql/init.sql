SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;
SET character_set_client = utf8mb4;
SET character_set_connection = utf8mb4;
SET character_set_results = utf8mb4;

-- Создание базы данных (если не существует)
CREATE DATABASE IF NOT EXISTS blog CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE blog;

-- Таблица категорий
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

-- Таблица постов (без статуса и комментариев)
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

-- Таблица связи постов с категориями (многие ко многим)
CREATE TABLE IF NOT EXISTS post_category (
    post_id INT,
    category_id INT,
    PRIMARY KEY (post_id, category_id),
    INDEX idx_category_id (category_id),
    INDEX idx_post_id (post_id),
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Заполнение категориями (из CategorySeeder)
INSERT INTO categories (name, description, slug, created_at) VALUES
('Новости', 'Свежие новости из мира технологий и разработки', 'novosti', NOW()),
('PHP', 'Всё о PHP: новые версии, лучшие практики, советы', 'php', NOW()),
('JavaScript', 'Современный JavaScript, фреймворки и библиотеки', 'javascript', NOW()),
('Базы данных', 'MySQL, PostgreSQL, MongoDB и другие СУБД', 'bazy-dannyh', NOW()),
('Инструменты', 'Полезные инструменты для разработчика', 'instrumenty', NOW()),
('Карьера', 'Советы по карьере в IT, собеседования, резюме', 'karera', NOW()),
('Python', 'Разработка на Python: веб, анализ данных, автоматизация', 'python', NOW()),
('Frontend', 'HTML, CSS, адаптивная верстка, UI/UX', 'frontend', NOW()),
('DevOps', 'CI/CD, контейнеризация, оркестрация, мониторинг', 'devops', NOW()),
('Алгоритмы и структуры данных', 'Подготовка к собеседованиям, оптимизация кода', 'algoritmy', NOW()),
('Go', 'Язык программирования Go: особенности, применение', 'go', NOW()),
('Мобильная разработка', 'iOS, Android, кроссплатформенные решения', 'mobilnaya-razrabotka', NOW()),
('Безопасность', 'Информационная безопасность, шифрование, защита данных', 'bezopasnost', NOW()),
('Искусственный интеллект', 'Машинное обучение, нейросети, AI в разработке', 'iskusstvennyj-intellekt', NOW()),
('Книги и ресурсы', 'Рекомендации книг, курсов, полезных ресурсов для разработчиков', 'knigi', NOW()),
('Архив', 'Старые, но полезные статьи', 'arhiv', NOW());

-- Заполнение постами (из PostSeeder)
INSERT INTO posts (title, description, content, image, views, slug, published_at, created_at) VALUES
-- PHP посты (10)
('Введение в PHP 8: новые возможности', 'Обзор ключевых нововведений в PHP 8: атрибуты, union types, match expression и другие', '<p>PHP 8 привнес множество значительных улучшений в язык. Рассмотрим ключевые нововведения:</p><h2>Атрибуты (Attributes)</h2><p>Атрибуты заменяют dockblock-аннотации и предоставляют структурированный способ добавления метаданных.</p><h2>Union Types</h2><p>Теперь можно указывать несколько возможных типов для одного параметра или возвращаемого значения.</p><h2>Match Expression</h2><p>Более мощная альтернатива switch с возвратом значения.</p><h2>Named Arguments</h2><p>Позволяют передавать аргументы по имени, а не по позиции.</p><p>Эти и многие другие улучшения делают PHP 8 отличным выбором для современных проектов.</p>', 'https://images.unsplash.com/photo-1498050108023-c5249f4df085?w=800&auto=format', 1250, 'vvedenie-v-php-8', DATE_SUB(NOW(), INTERVAL 1 DAY), NOW()),
('Новости PHP: вышел PHP 8.3', 'Обзор новых функций и улучшений в PHP 8.3', '<p>PHP 8.3 принес множество улучшений:</p><h2>Новые функции</h2><p>Улучшения в работе с массивами, новые методы классов.</p><h2>Производительность</h2><p>Оптимизация JIT и других компонентов.</p>', 'https://images.unsplash.com/photo-1461749280684-dccba630e2f6?w=800&auto=format', 520, 'novosti-php-vyshel-php-8-3', DATE_SUB(NOW(), INTERVAL 2 DAY), NOW()),
('Composer: управление зависимостями в PHP', 'Полное руководство по работе с Composer', '<p>Composer - менеджер зависимостей для PHP.</p><h2>Установка и настройка</h2><p>Как установить Composer и настроить autoload.</p><h2>composer.json</h2><p>Структура файла, версионирование зависимостей.</p><h2>Создание пакетов</h2><p>Как создать и опубликовать свой пакет на Packagist.</p>', 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=800&auto=format', 3400, 'composer-upravlenie-zavisimostyami-v-php', DATE_SUB(NOW(), INTERVAL 3 DAY), NOW()),
('Laravel vs Symfony: сравнение фреймворков', 'Что выбрать для проекта в 2024 году', '<p>Laravel и Symfony - два самых популярных PHP фреймворка.</p><h2>Laravel</h2><p>Простота использования, богатая экосистема, Eloquent ORM.</p><h2>Symfony</h2><p>Компонентная архитектура, гибкость, производительность.</p><h2>Что выбрать?</h2><p>Сравнение по ключевым критериям для вашего проекта.</p>', 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=800&auto=format', 5600, 'laravel-vs-symfony-sravnenie-frejmvorkov', DATE_SUB(NOW(), INTERVAL 4 DAY), NOW()),
('PSR стандарты в PHP', 'Что такое PSR и почему их важно соблюдать', '<p>PSR (PHP Standards Recommendations) - стандарты PHP.</p><h2>PSR-1 и PSR-2</h2><p>Базовые стандарты кодирования.</p><h2>PSR-4</h2><p>Autoloading стандарт.</p><h2>PSR-7</h2><p>HTTP message interfaces.</p><h2>PSR-12</h2><p>Расширенный стиль кодирования.</p>', 'https://images.unsplash.com/photo-1515879218367-8466d910aaa4?w=800&auto=format', 2100, 'psr-standarty-v-php', DATE_SUB(NOW(), INTERVAL 5 DAY), NOW()),
('ООП в PHP: продвинутые техники', 'Трейты, интерфейсы, абстрактные классы', '<p>Объектно-ориентированное программирование в PHP.</p><h2>Наследование</h2><p>Родительские и дочерние классы.</p><h2>Интерфейсы</h2><p>Контракты для классов.</p><h2>Трейты</h2><p>Горизонтальное переиспользование кода.</p><h2>Абстрактные классы</h2><p>Базовые классы с частичной реализацией.</p>', 'https://images.unsplash.com/photo-1550439062-609e1531270e?w=800&auto=format', 4300, 'oop-v-php-prodvinutye-tehniki', DATE_SUB(NOW(), INTERVAL 6 DAY), NOW()),
('Тестирование в PHP: PHPUnit и Pest', 'Как писать тесты и почему это важно', '<p>Тестирование - важная часть разработки.</p><h2>PHPUnit</h2><p>Классический фреймворк для тестирования.</p><h2>Pest</h2><p>Современный, элегантный синтаксис.</p><h2>Моки и стабы</h2><p>Изоляция тестируемого кода.</p><h2>TDD</h2><p>Разработка через тестирование.</p>', 'https://images.unsplash.com/photo-1542831371-29b0f74f9713?w=800&auto=format', 1850, 'testirovanie-v-php-phpunit-i-pest', DATE_SUB(NOW(), INTERVAL 7 DAY), NOW()),
('Асинхронность в PHP: ReactPHP и Swoole', 'Возможности асинхронного программирования', '<p>Асинхронное программирование в PHP.</p><h2>ReactPHP</h2><p>Событийно-ориентированное программирование.</p><h2>Swoole</h2><p>Высокопроизводительный сервер для PHP.</p><h2>Когда нужно?</h2><p>Real-time приложения, веб-сокеты.</p>', 'https://images.unsplash.com/photo-1543286386-713bdd548da4?w=800&auto=format', 980, 'asinhronnost-v-php-reactphp-i-swoole', DATE_SUB(NOW(), INTERVAL 8 DAY), NOW()),
('PHP и MySQL: работа с базами данных', 'PDO, prepared statements, транзакции', '<p>Работа с MySQL в PHP.</p><h2>PDO</h2><p>Универсальный интерфейс для работы с БД.</p><h2>Prepared Statements</h2><p>Защита от SQL-инъекций.</p><h2>Транзакции</h2><p>Группировка операций.</p>', 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=800&auto=format', 6700, 'php-i-mysql-rabota-s-bazami-dannyh', DATE_SUB(NOW(), INTERVAL 9 DAY), NOW()),
('Безопасность PHP-приложений', 'Защита от XSS, CSRF, SQL-инъекций', '<p>Безопасность PHP приложений.</p><h2>XSS</h2><p>Защита от межсайтового скриптинга.</p><h2>CSRF</h2><p>Защита от подделки межсайтовых запросов.</p><h2>SQL Injection</h2><p>Предотвращение SQL-инъекций.</p><h2>Хеширование паролей</h2><p>password_hash() и password_verify().</p>', 'https://images.unsplash.com/photo-1523961131990-5ea7c61b2107?w=800&auto=format', 3900, 'bezopasnost-php-prilozhenij', DATE_SUB(NOW(), INTERVAL 10 DAY), NOW()),

-- JavaScript посты
('Современный JavaScript: что нужно знать в 2024', 'Обзор актуальных возможностей ES6+ и популярных фреймворков', '<p>JavaScript продолжает активно развиваться. Вот что важно знать в 2024 году:</p><h2>ES2023 и ES2024</h2><p>Новые методы массивов, улучшения работы с промисами.</p><h2>Фреймворки</h2><p>React, Vue и Angular остаются основными игроками, но растет популярность Solid и Svelte.</p><h2>TypeScript</h2><p>Стал стандартом для крупных проектов.</p>', 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=800&auto=format', 980, 'sovremennyj-javascript-chto-nuzhno-znat-v-2024', DATE_SUB(NOW(), INTERVAL 11 DAY), NOW()),
('TypeScript: почему стоит использовать', 'Преимущества типизации в JavaScript проектах', '<p>TypeScript добавляет статическую типизацию в JavaScript.</p><h2>Преимущества</h2><p>Раннее обнаружение ошибок, лучшая поддержка IDE, самодокументируемый код.</p><h2>Интеграция</h2><p>Как начать использовать TypeScript в существующем проекте.</p>', 'https://images.unsplash.com/photo-1542831371-29b0f74f9713?w=800&auto=format', 890, 'typescript-pochemu-stoit-ispolzovat', DATE_SUB(NOW(), INTERVAL 12 DAY), NOW()),

-- Frontend посты
('Современный CSS: Grid и Flexbox', 'Полное руководство по современным методам верстки', '<p>Современный CSS предлагает мощные инструменты для верстки.</p><h2>Grid</h2><p>Двухмерная система раскладки.</p><h2>Flexbox</h2><p>Одномерная раскладка для компонентов.</p>', 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=800&auto=format', 1500, 'sovremennyj-css-grid-i-flexbox', DATE_SUB(NOW(), INTERVAL 13 DAY), NOW()),

-- Базы данных посты
('Оптимизация запросов MySQL', 'Практические советы по ускорению работы с базами данных', '<p>Оптимизация запросов критически важна для производительности приложений.</p><h2>Индексы</h2><p>Правильное использование индексов может ускорить запросы в сотни раз.</p><h2>EXPLAIN</h2><p>Анализ плана выполнения запросов помогает найти узкие места.</p>', 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=800&auto=format', 750, 'optimizaciya-zaprosov-mysql', DATE_SUB(NOW(), INTERVAL 14 DAY), NOW()),
('PostgreSQL vs MySQL: что выбрать?', 'Сравнение двух популярных СУБД', '<p>PostgreSQL предлагает множество возможностей, которых нет в MySQL.</p><h2>JSONB</h2><p>Эффективная работа с JSON данными.</p><h2>Полнотекстовый поиск</h2><p>Встроенные возможности поиска по тексту.</p>', 'https://images.unsplash.com/photo-1515879218367-8466d910aaa4?w=800&auto=format', 620, 'postgresql-vs-mysql-chto-vybrat', DATE_SUB(NOW(), INTERVAL 15 DAY), NOW()),

-- Инструменты посты
('Docker для разработчика: основы', 'Как начать использовать Docker в повседневной разработке', '<p>Docker изменил подход к разработке и деплою приложений.</p><h2>Основные понятия</h2><p>Контейнеры, образы, Dockerfile, docker-compose.</p><h2>Практическое применение</h2><p>Как организовать среду разработки с несколькими сервисами.</p>', 'https://images.unsplash.com/photo-1515879218367-8466d910aaa4?w=800&auto=format', 2100, 'docker-dlya-razrabotchika-osnovy', DATE_SUB(NOW(), INTERVAL 16 DAY), NOW()),
('Git: продвинутые техники', 'Rebase, cherry-pick и другие полезные команды', '<p>Освоив продвинутые техники Git, вы сможете эффективнее управлять историей проекта.</p><h2>Rebase</h2><p>Перемещение и объединение коммитов для чистой истории.</p><h2>Cherry-pick</h2><p>Выборочное применение коммитов из других веток.</p>', 'https://images.unsplash.com/photo-1523961131990-5ea7c61b2107?w=800&auto=format', 1450, 'git-prodvinutye-tehniki', DATE_SUB(NOW(), INTERVAL 17 DAY), NOW()),

-- Карьера
('Как успешно пройти собеседование в IT', 'Советы по подготовке к техническим интервью', '<p>Подготовка к собеседованию требует системного подхода.</p><h2>Техническая часть</h2><p>Алгоритмы, структуры данных, особенности языка.</p><h2>Soft skills</h2><p>Коммуникация, работа в команде, решение конфликтов.</p>', 'https://images.unsplash.com/photo-1550439062-609e1531270e?w=800&auto=format', 3500, 'kak-uspeshno-projti-sobesedovanie-v-it', DATE_SUB(NOW(), INTERVAL 18 DAY), NOW()),

-- Python
('Python для начинающих: с чего начать', 'Введение в Python для новичков', '<p>Python - один из самых популярных языков программирования.</p><h2>Простота изучения</h2><p>Чистый синтаксис и большое сообщество.</p><h2>Области применения</h2><p>Веб-разработка, анализ данных, автоматизация.</p>', 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=800&auto=format', 1800, 'python-dlya-nachinayushih-s-chego-nachat', DATE_SUB(NOW(), INTERVAL 19 DAY), NOW()),
('Django vs Flask: сравнение фреймворков', 'Что выбрать для веб-разработки на Python', '<p>Django - мощный веб-фреймворк для Python.</p><h2>Преимущества</h2><p>Встроенная админка, ORM, безопасность.</p>', 'https://images.unsplash.com/photo-1498050108023-c5249f4df085?w=800&auto=format', 950, 'django-vs-flask-sravnenie-frejmvorkov', DATE_SUB(NOW(), INTERVAL 20 DAY), NOW()),

-- DevOps
('CI/CD с GitHub Actions', 'Настройка непрерывной интеграции и доставки', '<p>CI/CD автоматизирует процесс разработки и доставки.</p><h2>GitHub Actions</h2><p>Простая настройка pipeline прямо в репозитории.</p>', 'https://images.unsplash.com/photo-1543286386-713bdd548da4?w=800&auto=format', 780, 'ci-cd-s-github-actions', DATE_SUB(NOW(), INTERVAL 21 DAY), NOW()),
('Kubernetes для начинающих', 'Основы оркестрации контейнеров', '<p>Kubernetes - стандарт оркестрации контейнеров.</p><h2>Основные понятия</h2><p>Pods, services, deployments, ingress.</p>', 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=800&auto=format', 630, 'kubernetes-dlya-nachinayushih', DATE_SUB(NOW(), INTERVAL 22 DAY), NOW()),

-- Алгоритмы
('Алгоритмы сортировки: подробный разбор', 'Пузырьковая, быстрая, сортировка слиянием', '<p>Понимание алгоритмов критически важно для программиста.</p><h2>Сортировка</h2><p>Различные алгоритмы и их сложность.</p>', 'https://images.unsplash.com/photo-1550439062-609e1531270e?w=800&auto=format', 2100, 'algoritmy-sortirovki-podrobnyj-razbor', DATE_SUB(NOW(), INTERVAL 23 DAY), NOW()),

-- Go
('Введение в Go: особенности языка', 'Почему Go становится популярным', '<p>Go сочетает производительность и простоту.</p><h2>Горутины</h2><p>Легковесные потоки для конкурентности.</p>', 'https://images.unsplash.com/photo-1542831371-29b0f74f9713?w=800&auto=format', 890, 'vvedenie-v-go-osobennosti-yazyka', DATE_SUB(NOW(), INTERVAL 24 DAY), NOW()),

-- Мобильная разработка
('React Native vs Flutter: что выбрать', 'Сравнение кроссплатформенных фреймворков', '<p>Кроссплатформенная разработка экономит ресурсы.</p><h2>React Native</h2><p>JavaScript, большой экосистема.</p><h2>Flutter</h2><p>Dart, отличная производительность.</p>', 'https://images.unsplash.com/photo-1461749280684-dccba630e2f6?w=800&auto=format', 1200, 'react-native-vs-flutter-chto-vybrat', DATE_SUB(NOW(), INTERVAL 25 DAY), NOW()),

-- Безопасность
('Основы безопасности веб-приложений', 'Защита от XSS, CSRF, SQL-инъекций', '<p>Безопасность должна быть встроена в процесс разработки.</p><h2>Основные уязвимости</h2><p>XSS, CSRF, SQLi, их предотвращение.</p>', 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=800&auto=format', 1100, 'osnovy-bezopasnosti-veb-prilozhenij', DATE_SUB(NOW(), INTERVAL 26 DAY), NOW()),

-- Искусственный интеллект
('Введение в машинное обучение', 'Основные концепции и алгоритмы ML', '<p>Машинное обучение меняет мир.</p><h2>Основные концепции</h2><p>Обучение с учителем и без, нейросети.</p>', 'https://images.unsplash.com/photo-1498050108023-c5249f4df085?w=800&auto=format', 2300, 'vvedenie-v-mashinnoe-obuchenie', DATE_SUB(NOW(), INTERVAL 27 DAY), NOW()),

-- Книги и ресурсы
('Топ-10 книг для программиста', 'Лучшие книги по программированию', '<p>Книги помогают углубить знания.</p><h2>Классика</h2><p>"Совершенный код", "Чистый код", "Программист-прагматик".</p>', 'https://images.unsplash.com/photo-1523961131990-5ea7c61b2107?w=800&auto=format', 3400, 'top-10-knig-dlya-programmista', DATE_SUB(NOW(), INTERVAL 28 DAY), NOW()),

-- Архив
('Архив: старая статья про PHP 7', 'Устаревшая, но всё ещё полезная информация', '<p>Когда-то это было актуально...</p>', 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=800&auto=format', 120, 'arhiv-staraya-statya-pro-php-7', DATE_SUB(NOW(), INTERVAL 29 DAY), NOW());

-- Заполнение связей постов с категориями
-- PHP посты (первые 10)
INSERT INTO post_category (post_id, category_id) VALUES
(1, 2), (2, 2), (2, 1), (3, 2), (3, 5), (4, 2), (5, 2), (6, 2), (7, 2), (8, 2), (9, 2), (9, 4), (10, 2), (10, 13),
-- JavaScript посты
(11, 3), (12, 3), (12, 8),
-- Frontend
(13, 8),
-- Базы данных
(14, 4), (15, 4),
-- Инструменты
(16, 5), (16, 9), (17, 5),
-- Карьера
(18, 6),
-- Python
(19, 7), (20, 7),
-- DevOps
(21, 9), (22, 9),
-- Алгоритмы
(23, 10),
-- Go
(24, 11),
-- Мобильная разработка
(25, 12), (25, 3),
-- Безопасность
(26, 13),
-- Искусственный интеллект
(27, 14),
-- Книги и ресурсы
(28, 15),
-- Архив
(29, 16);