# Use the official PHP image with Apache
FROM php:8.2-apache

# Enable Apache mod_rewrite (optional, but often useful)
RUN a2enmod rewrite

# Install necessary PHP extensions
RUN docker-php-ext-install mysqli

# Set working directory
WORKDIR /var/www/html

# Copy all project files to Apache server directory
COPY . /var/www/html/

# If you put your main PHP files inside /public, set that as the default web root
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Expose port 80 to the web
EXPOSE 80
