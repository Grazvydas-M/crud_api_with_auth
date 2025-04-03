# Use the official PHP image with FPM and necessary extensions
FROM php:8.2-fpm

# Set working directory
WORKDIR /var/www/html

# Install required packages
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libicu-dev \
    curl \
    libjpeg62-turbo-dev \
    libpng-dev \
    libfreetype6-dev \
    libmariadb-dev-compat \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install intl pdo pdo_mysql opcache gd

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy application files
COPY ../.. .

# Install Symfony dependencies
RUN composer install --no-interaction --optimize-autoloader

RUN chown -R www-data:www-data /var/www/html

# Copy custom PHP configuration (optional)
COPY docker/php/php.ini /usr/local/etc/php/conf.d/zz-local.ini

# Expose port
EXPOSE 9000

CMD ["php-fpm"]
