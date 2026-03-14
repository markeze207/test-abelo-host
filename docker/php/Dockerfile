FROM php:8.1-apache

# Установка необходимых расширений и локалей
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    locales \
    && docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    mysqli \
    zip \
    && echo "ru_RU.UTF-8 UTF-8" > /etc/locale.gen \
    && locale-gen \
    && update-locale LANG=ru_RU.UTF-8 \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Установка переменных окружения для локали
ENV LANG ru_RU.UTF-8
ENV LANGUAGE ru_RU:ru
ENV LC_ALL ru_RU.UTF-8

# Включение модулей Apache
RUN a2enmod rewrite

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Копирование конфигурации сайта
COPY docker/php/000-default.conf /etc/apache2/sites-available/000-default.conf

# Устанавливаем рабочую директорию
WORKDIR /var/www/html

# 1. СНАЧАЛА копируем ТОЛЬКО composer файлы (для кэширования)
COPY composer.json composer.lock* ./

# 2. ПОТОМ устанавливаем зависимости
RUN composer install --no-dev --no-interaction --optimize-autoloader

# 3. И ТОЛЬКО ПОТОМ копируем остальной код
COPY . .

# Создание директорий для Smarty с правильными правами
RUN mkdir -p /var/www/html/smarty/compiled \
    /var/www/html/smarty/cache \
    /var/www/html/smarty/config \
    /var/www/html/logs \
    && chmod -R 777 /var/www/html/smarty \
    && chmod -R 777 /var/www/html/logs

EXPOSE 80