FROM php:8.2-apache

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

# Install composer dependencies
RUN if [ -f composer.json ]; then composer install --no-dev --optimize-autoloader; fi

# Uploads directory
RUN mkdir -p uploads && chmod 777 uploads

# Enable Apache rewrite module
# This is necessary for .htaccess files to work correctly
RUN a2enmod rewrite

# Expose port 80 (Render will map to $PORT)
EXPOSE 80

# Start Apache in foreground
CMD ["apache2-foreground"]
