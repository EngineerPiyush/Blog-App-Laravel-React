FROM php:8.2-fpm

# Install system dependencies
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
    && docker-php-ext-install -j$(nproc) \
        gd \
        pdo_pgsql \
        pgsql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        zip \
        curl \
        fileinfo \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2.5 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy project files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Fix upload size (important for image upload)
RUN echo "upload_max_filesize=10M" > /usr/local/etc/php/conf.d/uploads.ini \
 && echo "post_max_size=12M" >> /usr/local/etc/php/conf.d/uploads.ini

# Clear Laravel cache
RUN php artisan config:clear && php artisan cache:clear

# Expose port
EXPOSE 8000

# Start app (RUN at runtime, not build)
CMD php artisan config:clear && \
    php artisan cache:clear && \
    php artisan migrate --force && \
    php artisan serve --host=0.0.0.0 --port=10000