# Use the official PHP 8.2 image with Apache
FROM php:8.2-apache

# Set the working directory inside the container
WORKDIR /var/www/html

# Install required PHP extensions
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql gd

# Enable Apache mod_rewrite (important for frameworks like Laravel)
RUN a2enmod rewrite

# Copy application files into the container
COPY . /var/www/html/

# Set correct permissions for the web server
RUN chown -R www-data:www-data /var/www/html \
    && find /var/www/html -type d -exec chmod 755 {} \; \
    && find /var/www/html -type f -exec chmod 644 {} \;

# Expose the default Apache port
EXPOSE 80

# Start Apache server
CMD ["apache2-foreground"]
