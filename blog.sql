-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Хост: mysql:3306
-- Время создания: Мар 10 2026 г., 22:18
-- Версия сервера: 8.0.45
-- Версия PHP: 8.3.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `blog`
--

-- --------------------------------------------------------

--
-- Структура таблицы `categories`
--

CREATE TABLE `categories` (
  `id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `slug`, `created_at`, `updated_at`) VALUES
(1, 'Новости', 'Свежие новости из мира технологий и разработки', 'novosti', '2026-03-11 01:17:50', '2026-03-10 22:17:50'),
(2, 'PHP', 'Всё о PHP: новые версии, лучшие практики, советы', 'php', '2026-03-11 01:17:50', '2026-03-10 22:17:50'),
(3, 'JavaScript', 'Современный JavaScript, фреймворки и библиотеки', 'javascript', '2026-03-11 01:17:50', '2026-03-10 22:17:50'),
(4, 'Базы данных', 'MySQL, PostgreSQL, MongoDB и другие СУБД', 'bazy-dannyh', '2026-03-11 01:17:50', '2026-03-10 22:17:50'),
(5, 'Инструменты', 'Полезные инструменты для разработчика', 'instrumenty', '2026-03-11 01:17:50', '2026-03-10 22:17:50'),
(6, 'Карьера', 'Советы по карьере в IT, собеседования, резюме', 'karera', '2026-03-11 01:17:50', '2026-03-10 22:17:50'),
(7, 'Python', 'Разработка на Python: веб, анализ данных, автоматизация', 'python', '2026-03-11 01:17:50', '2026-03-10 22:17:50'),
(8, 'Frontend', 'HTML, CSS, адаптивная верстка, UI/UX', 'frontend', '2026-03-11 01:17:50', '2026-03-10 22:17:50'),
(9, 'DevOps', 'CI/CD, контейнеризация, оркестрация, мониторинг', 'devops', '2026-03-11 01:17:50', '2026-03-10 22:17:50'),
(10, 'Алгоритмы и структуры данных', 'Подготовка к собеседованиям, оптимизация кода', 'algoritmy-i-struktury-dannyh', '2026-03-11 01:17:50', '2026-03-10 22:17:50'),
(11, 'Мобильная разработка', 'iOS, Android, кроссплатформенные решения', 'mobilnaya-razrabotka', '2026-03-11 01:17:50', '2026-03-10 22:17:50'),
(12, 'Безопасность', 'Информационная безопасность, шифрование, защита данных', 'bezopasnost', '2026-03-11 01:17:50', '2026-03-10 22:17:50'),
(13, 'Искусственный интеллект', 'Машинное обучение, нейросети, AI в разработке', 'iskusstvennyy-intellekt', '2026-03-11 01:17:50', '2026-03-10 22:17:50'),
(14, 'Книги и ресурсы', 'Рекомендации книг, курсов, полезных ресурсов для разработчиков', 'knigi-i-resursy', '2026-03-11 01:17:50', '2026-03-10 22:17:50'),
(15, 'Архив', 'Старые, но полезные статьи', 'arhiv', '2026-03-11 01:17:50', '2026-03-10 22:17:50');

-- --------------------------------------------------------

--
-- Структура таблицы `posts`
--

CREATE TABLE `posts` (
  `id` int NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `content` longtext COLLATE utf8mb4_unicode_ci,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `views` int DEFAULT '0',
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('draft','published') COLLATE utf8mb4_unicode_ci DEFAULT 'published',
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `posts`
--

INSERT INTO `posts` (`id`, `title`, `description`, `content`, `image`, `views`, `slug`, `status`, `published_at`, `created_at`, `updated_at`) VALUES
(1, 'Введение в PHP 8: новые возможности', 'Обзор ключевых нововведений в PHP 8: атрибуты, union types, match expression и другие', '<p>PHP 8 привнес множество значительных улучшений в язык. Рассмотрим ключевые нововведения:</p>\r\n            \r\n            <h2>Атрибуты (Attributes)</h2>\r\n            <p>Атрибуты заменяют dockblock-аннотации и предоставляют структурированный способ добавления метаданных.</p>\r\n            \r\n            <h2>Union Types</h2>\r\n            <p>Теперь можно указывать несколько возможных типов для одного параметра или возвращаемого значения.</p>\r\n            \r\n            <h2>Match Expression</h2>\r\n            <p>Более мощная альтернатива switch с возвратом значения.</p>\r\n            \r\n            <h2>Named Arguments</h2>\r\n            <p>Позволяют передавать аргументы по имени, а не по позиции.</p>\r\n            \r\n            <p>Эти и многие другие улучшения делают PHP 8 отличным выбором для современных проектов.</p>', 'https://images.unsplash.com/photo-1498050108023-c5249f4df085?w=800&auto=format', 1250, 'vvedenie-v-php-8-novye-vozmozhnosti', 'published', NULL, '2026-02-14 01:17:51', '2026-02-14 01:17:51'),
(2, 'Новости PHP: вышел PHP 8.3', 'Обзор новых функций и улучшений в PHP 8.3', '<p>PHP 8.3 принес множество улучшений:</p>\r\n            \r\n            <h2>Новые функции</h2>\r\n            <p>Улучшения в работе с массивами, новые методы классов.</p>\r\n            \r\n            <h2>Производительность</h2>\r\n            <p>Оптимизация JIT и других компонентов.</p>', 'https://images.unsplash.com/photo-1461749280684-dccba630e2f6?w=800&auto=format', 520, 'novosti-php-vyshel-php-8-3', 'published', NULL, '2026-02-27 01:17:51', '2026-02-27 01:17:51'),
(3, 'Composer: управление зависимостями в PHP', 'Полное руководство по работе с Composer', '<p>Composer - менеджер зависимостей для PHP.</p>\r\n            \r\n            <h2>Установка и настройка</h2>\r\n            <p>Как установить Composer и настроить autoload.</p>\r\n            \r\n            <h2>composer.json</h2>\r\n            <p>Структура файла, версионирование зависимостей.</p>\r\n            \r\n            <h2>Создание пакетов</h2>\r\n            <p>Как создать и опубликовать свой пакет на Packagist.</p>', 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=800&auto=format', 3400, 'composer-upravlenie-zavisimostyami-v-php', 'published', NULL, '2026-02-17 01:17:51', '2026-02-17 01:17:51'),
(4, 'Laravel vs Symfony: сравнение фреймворков', 'Что выбрать для проекта в 2024 году', '<p>Laravel и Symfony - два самых популярных PHP фреймворка.</p>\r\n            \r\n            <h2>Laravel</h2>\r\n            <p>Простота использования, богатая экосистема, Eloquent ORM.</p>\r\n            \r\n            <h2>Symfony</h2>\r\n            <p>Компонентная архитектура, гибкость, производительность.</p>\r\n            \r\n            <h2>Что выбрать?</h2>\r\n            <p>Сравнение по ключевым критериям для вашего проекта.</p>', 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=800&auto=format', 5600, 'laravel-vs-symfony-sravnenie-freymvorkov', 'published', NULL, '2026-02-27 01:17:51', '2026-02-27 01:17:51'),
(5, 'PSR стандарты в PHP', 'Что такое PSR и почему их важно соблюдать', '<p>PSR (PHP Standards Recommendations) - стандарты PHP.</p>\r\n            \r\n            <h2>PSR-1 и PSR-2</h2>\r\n            <p>Базовые стандарты кодирования.</p>\r\n            \r\n            <h2>PSR-4</h2>\r\n            <p>Autoloading стандарт.</p>\r\n            \r\n            <h2>PSR-7</h2>\r\n            <p>HTTP message interfaces.</p>\r\n            \r\n            <h2>PSR-12</h2>\r\n            <p>Расширенный стиль кодирования.</p>', 'https://images.unsplash.com/photo-1515879218367-8466d910aaa4?w=800&auto=format', 2100, 'psr-standarty-v-php', 'published', NULL, '2026-02-25 01:17:51', '2026-02-25 01:17:51'),
(6, 'ООП в PHP: продвинутые техники', 'Трейты, интерфейсы, абстрактные классы', '<p>Объектно-ориентированное программирование в PHP.</p>\r\n            \r\n            <h2>Наследование</h2>\r\n            <p>Родительские и дочерние классы.</p>\r\n            \r\n            <h2>Интерфейсы</h2>\r\n            <p>Контракты для классов.</p>\r\n            \r\n            <h2>Трейты</h2>\r\n            <p>Горизонтальное переиспользование кода.</p>\r\n            \r\n            <h2>Абстрактные классы</h2>\r\n            <p>Базовые классы с частичной реализацией.</p>', 'https://images.unsplash.com/photo-1550439062-609e1531270e?w=800&auto=format', 4300, 'oop-v-php-prodvinutye-tehniki', 'published', NULL, '2026-02-22 01:17:51', '2026-02-22 01:17:51'),
(7, 'Тестирование в PHP: PHPUnit и Pest', 'Как писать тесты и почему это важно', '<p>Тестирование - важная часть разработки.</p>\r\n            \r\n            <h2>PHPUnit</h2>\r\n            <p>Классический фреймворк для тестирования.</p>\r\n            \r\n            <h2>Pest</h2>\r\n            <p>Современный, элегантный синтаксис.</p>\r\n            \r\n            <h2>Моки и стабы</h2>\r\n            <p>Изоляция тестируемого кода.</p>\r\n            \r\n            <h2>TDD</h2>\r\n            <p>Разработка через тестирование.</p>', 'https://images.unsplash.com/photo-1542831371-29b0f74f9713?w=800&auto=format', 1850, 'testirovanie-v-php-phpunit-i-pest', 'published', NULL, '2026-02-25 01:17:51', '2026-02-25 01:17:51'),
(8, 'Асинхронность в PHP: ReactPHP и Swoole', 'Возможности асинхронного программирования', '<p>Асинхронное программирование в PHP.</p>\r\n            \r\n            <h2>ReactPHP</h2>\r\n            <p>Событийно-ориентированное программирование.</p>\r\n            \r\n            <h2>Swoole</h2>\r\n            <p>Высокопроизводительный сервер для PHP.</p>\r\n            \r\n            <h2>Когда нужно?</h2>\r\n            <p>Real-time приложения, веб-сокеты.</p>', 'https://images.unsplash.com/photo-1543286386-713bdd548da4?w=800&auto=format', 980, 'asinhronnost-v-php-reactphp-i-swoole', 'published', NULL, '2026-02-27 01:17:51', '2026-02-27 01:17:51'),
(9, 'PHP и MySQL: работа с базами данных', 'PDO, prepared statements, транзакции', '<p>Работа с MySQL в PHP.</p>\r\n            \r\n            <h2>PDO</h2>\r\n            <p>Универсальный интерфейс для работы с БД.</p>\r\n            \r\n            <h2>Prepared Statements</h2>\r\n            <p>Защита от SQL-инъекций.</p>\r\n            \r\n            <h2>Транзакции</h2>\r\n            <p>Группировка операций.</p>', 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=800&auto=format', 6700, 'php-i-mysql-rabota-s-bazami-dannyh', 'published', NULL, '2026-02-13 01:17:51', '2026-02-13 01:17:51'),
(10, 'Безопасность PHP-приложений', 'Защита от XSS, CSRF, SQL-инъекций', '<p>Безопасность PHP приложений.</p>\r\n            \r\n            <h2>XSS</h2>\r\n            <p>Защита от межсайтового скриптинга.</p>\r\n            \r\n            <h2>CSRF</h2>\r\n            <p>Защита от подделки межсайтовых запросов.</p>\r\n            \r\n            <h2>SQL Injection</h2>\r\n            <p>Предотвращение SQL-инъекций.</p>\r\n            \r\n            <h2>Хеширование паролей</h2>\r\n            <p>password_hash() и password_verify().</p>', 'https://images.unsplash.com/photo-1523961131990-5ea7c61b2107?w=800&auto=format', 3900, 'bezopasnost-php-prilozheniy', 'published', NULL, '2026-02-11 01:17:51', '2026-02-11 01:17:51'),
(11, 'Современный JavaScript: что нужно знать в 2024', 'Обзор актуальных возможностей ES6+ и популярных фреймворков', '<p>JavaScript продолжает активно развиваться. Вот что важно знать в 2024 году:</p>\r\n            \r\n            <h2>ES2023 и ES2024</h2>\r\n            <p>Новые методы массивов, улучшения работы с промисами.</p>\r\n            \r\n            <h2>Фреймворки</h2>\r\n            <p>React, Vue и Angular остаются основными игроками, но растет популярность Solid и Svelte.</p>\r\n            \r\n            <h2>TypeScript</h2>\r\n            <p>Стал стандартом для крупных проектов.</p>', 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=800&auto=format', 980, 'sovremennyy-javascript-chto-nuzhno-znat-v-2024', 'published', NULL, '2026-02-20 01:17:51', '2026-02-20 01:17:51'),
(12, 'TypeScript: почему стоит использовать', 'Преимущества типизации в JavaScript проектах', '<p>TypeScript добавляет статическую типизацию в JavaScript.</p>\r\n            \r\n            <h2>Преимущества</h2>\r\n            <p>Раннее обнаружение ошибок, лучшая поддержка IDE, самодокументируемый код.</p>\r\n            \r\n            <h2>Интеграция</h2>\r\n            <p>Как начать использовать TypeScript в существующем проекте.</p>', 'https://images.unsplash.com/photo-1542831371-29b0f74f9713?w=800&auto=format', 890, 'typescript-pochemu-stoit-ispolzovat', 'published', NULL, '2026-03-05 01:17:51', '2026-03-05 01:17:51'),
(13, 'Современный CSS: Grid и Flexbox', 'Полное руководство по современным методам верстки', '<p>Современный CSS предлагает мощные инструменты для верстки.</p>\r\n            \r\n            <h2>Grid</h2>\r\n            <p>Двухмерная система раскладки.</p>\r\n            \r\n            <h2>Flexbox</h2>\r\n            <p>Одномерная раскладка для компонентов.</p>', 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=800&auto=format', 1500, 'sovremennyy-css-grid-i-flexbox', 'published', NULL, '2026-02-24 01:17:51', '2026-02-24 01:17:51'),
(14, 'Оптимизация запросов MySQL', 'Практические советы по ускорению работы с базами данных', '<p>Оптимизация запросов критически важна для производительности приложений.</p>\r\n            \r\n            <h2>Индексы</h2>\r\n            <p>Правильное использование индексов может ускорить запросы в сотни раз.</p>\r\n            \r\n            <h2>EXPLAIN</h2>\r\n            <p>Анализ плана выполнения запросов помогает найти узкие места.</p>', 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=800&auto=format', 750, 'optimizaciya-zaprosov-mysql', 'published', NULL, '2026-02-24 01:17:51', '2026-02-24 01:17:51'),
(15, 'PostgreSQL vs MySQL: что выбрать?', 'Сравнение двух популярных СУБД', '<p>PostgreSQL предлагает множество возможностей, которых нет в MySQL.</p>\r\n            \r\n            <h2>JSONB</h2>\r\n            <p>Эффективная работа с JSON данными.</p>\r\n            \r\n            <h2>Полнотекстовый поиск</h2>\r\n            <p>Встроенные возможности поиска по тексту.</p>', 'https://images.unsplash.com/photo-1515879218367-8466d910aaa4?w=800&auto=format', 620, 'postgresql-vs-mysql-chto-vybrat', 'published', NULL, '2026-02-19 01:17:51', '2026-02-19 01:17:51'),
(16, 'Docker для разработчика: основы', 'Как начать использовать Docker в повседневной разработке', '<p>Docker изменил подход к разработке и деплою приложений.</p>\r\n            \r\n            <h2>Основные понятия</h2>\r\n            <p>Контейнеры, образы, Dockerfile, docker-compose.</p>\r\n            \r\n            <h2>Практическое применение</h2>\r\n            <p>Как организовать среду разработки с несколькими сервисами.</p>', 'https://images.unsplash.com/photo-1515879218367-8466d910aaa4?w=800&auto=format', 2100, 'docker-dlya-razrabotchika-osnovy', 'published', NULL, '2026-02-09 01:17:51', '2026-02-09 01:17:51'),
(17, 'Git: продвинутые техники', 'Rebase, cherry-pick и другие полезные команды', '<p>Освоив продвинутые техники Git, вы сможете эффективнее управлять историей проекта.</p>\r\n            \r\n            <h2>Rebase</h2>\r\n            <p>Перемещение и объединение коммитов для чистой истории.</p>\r\n            \r\n            <h2>Cherry-pick</h2>\r\n            <p>Выборочное применение коммитов из других веток.</p>', 'https://images.unsplash.com/photo-1523961131990-5ea7c61b2107?w=800&auto=format', 1450, 'git-prodvinutye-tehniki', 'published', NULL, '2026-02-25 01:17:51', '2026-02-25 01:17:51'),
(18, 'Как успешно пройти собеседование в IT', 'Советы по подготовке к техническим интервью', '<p>Подготовка к собеседованию требует системного подхода.</p>\r\n            \r\n            <h2>Техническая часть</h2>\r\n            <p>Алгоритмы, структуры данных, особенности языка.</p>\r\n            \r\n            <h2>Soft skills</h2>\r\n            <p>Коммуникация, работа в команде, решение конфликтов.</p>', 'https://images.unsplash.com/photo-1550439062-609e1531270e?w=800&auto=format', 3500, 'kak-uspeshno-proyti-sobesedovanie-v-it', 'published', NULL, '2026-02-14 01:17:51', '2026-02-14 01:17:51'),
(19, 'Python для начинающих: с чего начать', 'Введение в Python для новичков', '<p>Python - один из самых популярных языков программирования.</p>\r\n            \r\n            <h2>Простота изучения</h2>\r\n            <p>Чистый синтаксис и большое сообщество.</p>\r\n            \r\n            <h2>Области применения</h2>\r\n            <p>Веб-разработка, анализ данных, автоматизация.</p>', 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=800&auto=format', 1800, 'python-dlya-nachinayuschih-s-chego-nachat', 'published', NULL, '2026-02-18 01:17:51', '2026-02-18 01:17:51'),
(20, 'Django vs Flask: сравнение фреймворков', 'Что выбрать для веб-разработки на Python', '<p>Django - мощный веб-фреймворк для Python.</p>\r\n            \r\n            <h2>Преимущества</h2>\r\n            <p>Встроенная админка, ORM, безопасность.</p>', 'https://images.unsplash.com/photo-1498050108023-c5249f4df085?w=800&auto=format', 950, 'django-vs-flask-sravnenie-freymvorkov', 'published', NULL, '2026-03-10 01:17:51', '2026-03-10 01:17:51'),
(21, 'CI/CD с GitHub Actions', 'Настройка непрерывной интеграции и доставки', '<p>CI/CD автоматизирует процесс разработки и доставки.</p>\r\n            \r\n            <h2>GitHub Actions</h2>\r\n            <p>Простая настройка pipeline прямо в репозитории.</p>', 'https://images.unsplash.com/photo-1543286386-713bdd548da4?w=800&auto=format', 780, 'ci-cd-s-github-actions', 'published', NULL, '2026-03-05 01:17:51', '2026-03-05 01:17:51'),
(22, 'Kubernetes для начинающих', 'Основы оркестрации контейнеров', '<p>Kubernetes - стандарт оркестрации контейнеров.</p>\r\n            \r\n            <h2>Основные понятия</h2>\r\n            <p>Pods, services, deployments, ingress.</p>', 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=800&auto=format', 630, 'kubernetes-dlya-nachinayuschih', 'published', NULL, '2026-03-01 01:17:51', '2026-03-01 01:17:51'),
(23, 'Алгоритмы сортировки: подробный разбор', 'Пузырьковая, быстрая, сортировка слиянием', '<p>Понимание алгоритмов критически важно для программиста.</p>\r\n            \r\n            <h2>Сортировка</h2>\r\n            <p>Различные алгоритмы и их сложность.</p>', 'https://images.unsplash.com/photo-1550439062-609e1531270e?w=800&auto=format', 2100, 'algoritmy-sortirovki-podrobnyy-razbor', 'published', NULL, '2026-02-23 01:17:51', '2026-02-23 01:17:51'),
(24, 'Введение в Go: особенности языка', 'Почему Go становится популярным', '<p>Go сочетает производительность и простоту.</p>\r\n            \r\n            <h2>Горутины</h2>\r\n            <p>Легковесные потоки для конкурентности.</p>', 'https://images.unsplash.com/photo-1542831371-29b0f74f9713?w=800&auto=format', 890, 'vvedenie-v-go-osobennosti-yazyka', 'published', NULL, '2026-02-25 01:17:51', '2026-02-25 01:17:51'),
(25, 'React Native vs Flutter: что выбрать', 'Сравнение кроссплатформенных фреймворков', '<p>Кроссплатформенная разработка экономит ресурсы.</p>\r\n            \r\n            <h2>React Native</h2>\r\n            <p>JavaScript, большой экосистема.</p>\r\n            \r\n            <h2>Flutter</h2>\r\n            <p>Dart, отличная производительность.</p>', 'https://images.unsplash.com/photo-1461749280684-dccba630e2f6?w=800&auto=format', 1200, 'react-native-vs-flutter-chto-vybrat', 'published', NULL, '2026-02-17 01:17:51', '2026-02-17 01:17:51'),
(26, 'Основы безопасности веб-приложений', 'Защита от XSS, CSRF, SQL-инъекций', '<p>Безопасность должна быть встроена в процесс разработки.</p>\r\n            \r\n            <h2>Основные уязвимости</h2>\r\n            <p>XSS, CSRF, SQLi, их предотвращение.</p>', 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=800&auto=format', 1100, 'osnovy-bezopasnosti-veb-prilozheniy', 'published', NULL, '2026-02-28 01:17:51', '2026-02-28 01:17:51'),
(27, 'Введение в машинное обучение', 'Основные концепции и алгоритмы ML', '<p>Машинное обучение меняет мир.</p>\r\n            \r\n            <h2>Основные концепции</h2>\r\n            <p>Обучение с учителем и без, нейросети.</p>', 'https://images.unsplash.com/photo-1498050108023-c5249f4df085?w=800&auto=format', 2300, 'vvedenie-v-mashinnoe-obuchenie', 'published', NULL, '2026-02-09 01:17:51', '2026-02-09 01:17:51'),
(28, 'Топ-10 книг для программиста', 'Лучшие книги по программированию', '<p>Книги помогают углубить знания.</p>\r\n            \r\n            <h2>Классика</h2>\r\n            <p>\"Совершенный код\", \"Чистый код\", \"Программист-прагматик\".</p>', 'https://images.unsplash.com/photo-1523961131990-5ea7c61b2107?w=800&auto=format', 3400, 'top-10-knig-dlya-programmista', 'published', NULL, '2026-02-13 01:17:51', '2026-02-13 01:17:51'),
(29, 'Архив: старая статья про PHP 7', 'Устаревшая, но всё ещё полезная информация', 'Когда-то это было актуально...', 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=800&auto=format', 120, 'arhiv-staraya-statya-pro-php-7', 'published', NULL, '2026-02-19 01:17:51', '2026-02-19 01:17:51');

-- --------------------------------------------------------

--
-- Структура таблицы `post_category`
--

CREATE TABLE `post_category` (
  `post_id` int NOT NULL,
  `category_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `post_category`
--

INSERT INTO `post_category` (`post_id`, `category_id`) VALUES
(2, 1),
(1, 2),
(2, 2),
(3, 2),
(4, 2),
(5, 2),
(6, 2),
(7, 2),
(8, 2),
(9, 2),
(10, 2),
(11, 3),
(12, 3),
(25, 3),
(9, 4),
(14, 4),
(15, 4),
(3, 5),
(16, 5),
(17, 5),
(18, 6),
(19, 7),
(20, 7),
(12, 8),
(13, 8),
(16, 9),
(21, 9),
(22, 9),
(23, 10),
(25, 11),
(10, 12),
(26, 12),
(27, 13),
(28, 14),
(29, 15);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_slug` (`slug`);

--
-- Индексы таблицы `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_published_at` (`published_at`),
  ADD KEY `idx_views` (`views`),
  ADD KEY `idx_slug` (`slug`);
ALTER TABLE `posts` ADD FULLTEXT KEY `idx_search` (`title`,`description`,`content`);

--
-- Индексы таблицы `post_category`
--
ALTER TABLE `post_category`
  ADD PRIMARY KEY (`post_id`,`category_id`),
  ADD KEY `category_id` (`category_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT для таблицы `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `post_category`
--
ALTER TABLE `post_category`
  ADD CONSTRAINT `post_category_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `post_category_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
