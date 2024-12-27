FROM php:8.2-fpm

# Installing linux packages and php extensions
RUN apt-get update && apt-get install -y \
    curl \
    zip \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-install mbstring exif gd

# Copying composer from pre-build docker image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Setting the working directory
WORKDIR /var/www/html

# Copy application code
COPY . .

# Giving executable permission to the file
RUN chmod +x /var/www/html/docker-entrypoint.sh

# Set permissions
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 8000

ENTRYPOINT ["/var/www/html/docker-entrypoint.sh"]
