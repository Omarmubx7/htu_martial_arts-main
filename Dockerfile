FROM php:8.2-apache

RUN docker-php-ext-install mysqli pdo_mysql && \
    a2enmod rewrite

ARG XDEBUG=0

# Copy PHP configuration if needed in the future
COPY --chown=www-data:www-data . /var/www/html

# Use development php.ini as a starting point and allow overrides via /usr/local/etc/php/conf.d/
RUN cp /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini

EXPOSE 80
CMD ["apache2-foreground"]
