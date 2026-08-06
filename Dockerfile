FROM php:8.3-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends libsqlite3-dev \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install pdo pdo_sqlite

COPY . /var/www/html/

RUN find /var/www/html -name "*.sh" -exec chmod +x {} \; \
    && chown -R www-data:www-data /var/www/html

EXPOSE 80
