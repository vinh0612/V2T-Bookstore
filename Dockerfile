# Xài bản PHP 8.2 kèm Apache làm máy chủ web
FROM php:8.2-apache

# Cài đặt Node.js (cần thiết để build giao diện Vite) và các thư viện PHP
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get update && apt-get install -y \
    nodejs \
    libzip-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-install pdo_mysql zip

# Bật mod_rewrite để Laravel điều hướng được link
RUN a2enmod rewrite

# Chỉ định thư mục gốc là /public
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Bê toàn bộ code vào máy chủ
WORKDIR /var/www/html
COPY . .

# Cài đặt Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# Cài đặt NPM và build file CSS/JS của Vite
RUN npm install
RUN npm run build

# Phân quyền cho Laravel ghi file log, cache và build
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public/build