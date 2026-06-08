FROM php:8.3-apache

# pdo_sqlite : libsqlite3-dev par sécurité (selon les versions d'image
# l'extension n'est pas toujours compilée par défaut)
RUN apt-get update \
    && apt-get install -y --no-install-recommends libsqlite3-dev \
    && docker-php-ext-install pdo_sqlite \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN printf 'date.timezone=Europe/Paris\n' > /usr/local/etc/php/conf.d/timezone.ini

COPY travaux-orange-sql/ /var/www/html/

RUN mkdir -p /var/www/html/data \
    && chown -R www-data:www-data /var/www/html/data