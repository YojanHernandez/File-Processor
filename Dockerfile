FROM php:8.2-apache

# Install required PHP extensions
RUN apt-get update \
    && apt-get install -y libcurl4-openssl-dev libonig-dev \
    && docker-php-ext-install curl

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

# Install Composer dependencies (with validation and verbose output for debugging)
RUN ls -l && composer validate && if [ -f composer.json ]; then composer install --no-dev --optimize-autoloader -v; fi

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Expose port 80
EXPOSE 80

# Default command
CMD ["apache2-foreground"]
