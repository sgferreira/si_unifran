FROM php:8.3-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends libonig-dev \
    && docker-php-ext-install pdo_mysql mbstring \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri -e "s!/var/www/html!${APACHE_DOCUMENT_ROOT}!g" /etc/apache2/sites-available/*.conf \
    && printf '%s\n' \
       '<Directory /var/www/html/public>' \
       '  Options FollowSymLinks' \
       '  AllowOverride All' \
       '  Require all granted' \
       '</Directory>' > /etc/apache2/conf-available/doce-instante.conf \
    && a2enconf doce-instante

WORKDIR /var/www/html
COPY . /var/www/html
