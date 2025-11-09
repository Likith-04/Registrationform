# Use official PHP + Apache image
FROM php:8.2-apache

# Enable mysqli extension
RUN docker-php-ext-install mysqli

# Copy source code
COPY . /var/www/html/

# Copy our Apache configuration
COPY apache.conf /etc/apache2/sites-available/000-default.conf

# Enable Apache rewrite
RUN a2enmod rewrite
