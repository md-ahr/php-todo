# Production-style image: Apache + PHP extensions, document root = /public
# Build: docker build -t php-todo .
# Run (example; set env for MySQL): docker run -p 8080:80 -e DB_HOST=... php-todo
#
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json ./
RUN composer install --no-dev --no-interaction --optimize-autoloader

FROM php:8.2-apache

RUN docker-php-ext-install -j$(nproc) pdo_mysql

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

# Point Apache & PHP docroot at /public; allow .htaccess rewrites
RUN sed -ri 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/sites-available/*.conf \
    && sed -ri '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf \
    && a2enmod rewrite

WORKDIR /var/www/html
COPY --from=vendor /app/vendor ./vendor
COPY . .

# App expects .env or real env vars (DB_*, APP_*).
RUN chmod -R a+rX /var/www/html

EXPOSE 80
