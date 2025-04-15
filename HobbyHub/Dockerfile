FROM php:8.2-apache

# Install extensions
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy source code
COPY ./public/ /var/www/html/
COPY ./includes/ /var/www/html/includes/

# Set working directory
WORKDIR /var/www/html/

# Permissions
RUN chown -R www-data:www-data /var/www/html/

EXPOSE 80
