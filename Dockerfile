# Use official PHP image with Apache
FROM php:8.2-apache

# Install mysqli and pdo_mysql extensions for MySQL
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copy your app files to Apache's web directory
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html

# Ensure index.php is the default entry point
RUN echo "DirectoryIndex index.php" >> /etc/apache2/apache2.conf

# Expose port 80
EXPOSE 80

# Start Apache in foreground
CMD ["apache2-foreground"]
