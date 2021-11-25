FROM php:7.4-apache

WORKDIR /var/www/html
RUN docker-php-ext-install tokenizer mysqli pdo_mysql
RUN chown -R www-data:www-data /var/www/html && a2enmod rewrite
EXPOSE 80