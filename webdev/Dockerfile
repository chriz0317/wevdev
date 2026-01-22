# Use the official PHP image with Apache
FROM php:8.2-apache

# Copy your project files to the web server root
COPY . /var/www/html/

# Enable Apache mod_rewrite (optional, but useful for routing)
RUN a2enmod rewrite

# Tell Render which port we are listening on
EXPOSE 80
