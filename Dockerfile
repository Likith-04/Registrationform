# Use PHP with Apache
FROM php:8.2-apache

# Enable mysqli
RUN docker-php-ext-install mysqli

# Copy project files into Apache root
COPY . /var/www/html/

# Ensure index.php loads first
RUN echo "DirectoryIndex index.php" >> /etc/apache2/apache2.conf

# Expose port
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
