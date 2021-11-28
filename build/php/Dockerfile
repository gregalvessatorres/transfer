FROM php:7.4-apache

WORKDIR /var/www/html
RUN docker-php-ext-install tokenizer mysqli pdo_mysql
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
php -r "if (hash_file('sha384', 'composer-setup.php') === '906a84df04cea2aa72f40b5f787e49f22d4c2f19492ac310e8cba5b96ac8b64115ac402c8cd292b8a03482574915d1a8') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" && \
php composer-setup.php && \
php -r "unlink('composer-setup.php');"
RUN mv composer.phar /usr/local/bin/composer
RUN chown -R www-data:www-data /var/www/html && a2enmod rewrite
EXPOSE 80