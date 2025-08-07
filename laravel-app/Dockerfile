FROM php:8.2-fpm

# تثبيت بعض الأدوات اللي بيلزمها Laravel
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    libzip-dev \
    libpq-dev \
    && docker-php-ext-install pdo_mysql mbstring zip exif pcntl

# تثبيت Composer (مدير الحزم للـ PHP)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# نسخ ملفات Laravel إلى الحاوية
WORKDIR /var/www
COPY . .

# تثبيت الـ dependencies
RUN composer install --optimize-autoloader

# تعيين صلاحيات مناسبة
RUN chown -R www-data:www-data /var/www && chmod -R 755 /var/www

EXPOSE 9000
CMD ["php-fpm"]
