FROM php:8.2-apache

# Install required system libraries and PHP extensions
RUN docker-php-ext-install pdo pdo_mysql

# Enable Apache mod_rewrite (useful for SEO friendly routes and general PHP frameworks)
RUN a2enmod rewrite

# Set the working directory inside the container
WORKDIR /var/www/html

# Copy the local source files into the container's document root
COPY . /var/www/html/

# Expose HTTP port
EXPOSE 80
