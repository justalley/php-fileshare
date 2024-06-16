FROM php:apache

RUN docker-php-ext-install mysqli pdo pdo_mysql

COPY . /var/www/html/

WORKDIR /var/www/html/

RUN chmod -R 775 /var/www/html/uploads \
    && chown -R www-data:www-data /var/www/html/uploads

EXPOSE 80

CMD ["apache2-foreground"]
