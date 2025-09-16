FROM php:8.2-fpm

# Install system dependencies + PostgreSQL libraries
RUN apt-get update && apt-get install -y \
    build-essential \
    curl \
    git \
    unzip \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    zip \
    && docker-php-ext-install pdo_pgsql pgsql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:2.5 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy project files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Expose port 8000
EXPOSE 8000

# Run migrations automatically, then start Laravel server
CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=10000
