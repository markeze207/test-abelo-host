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
    && update-locale LANG=ru_RU.UTF-8

# Установка переменных окружения для локали
ENV LANG ru_RU.UTF-8
ENV LANGUAGE ru_RU:ru
ENV LC_ALL ru_RU.UTF-8

# Включение модулей Apache
RUN a2enmod rewrite

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Копирование конфигурации сайта (исправленный путь)
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf

# Создание директорий для Smarty с правильными правами
RUN mkdir -p /var/www/html/smarty/compiled \
    /var/www/html/smarty/cache \
    /var/www/html/smarty/config \
    && chmod -R 777 /var/www/html/smarty

# Создание директории для логов
RUN mkdir -p /var/www/html/logs && chmod 777 /var/www/html/logs

WORKDIR /var/www/html

EXPOSE 80