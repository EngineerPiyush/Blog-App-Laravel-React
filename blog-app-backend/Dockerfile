FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    build-essential \
    git \
    unzip \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    libzip-dev \
    libcurl4-openssl-dev \
    zip \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo_pgsql pgsql mbstring exif pcntl bcmath zip curl fileinfo \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2.5 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Generate key (safe)
RUN php artisan key:generate

# Clear cache (important)
RUN php artisan config:clear && php artisan cache:clear

# PHP upload limits (optional but good)
RUN echo "upload_max_filesize=10M" >> /usr/local/etc/php/php.ini \
 && echo "post_max_size=10M" >> /usr/local/etc/php/php.ini

EXPOSE 8000

CMD php artisan config:clear && php artisan cache:clear && php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=10000