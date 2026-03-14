FROM node:20-alpine AS frontend-builder
WORKDIR /app

COPY package*.json ./
RUN npm ci

COPY resources ./resources
COPY public ./public
COPY vite.config.js postcss.config.js tailwind.config.js ./
RUN npm run build

FROM composer:2 AS vendor-builder
WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install \
  --no-dev \
  --no-interaction \
  --no-progress \
  --prefer-dist \
  --optimize-autoloader

FROM php:8.2-cli-alpine
WORKDIR /var/www/html

RUN apk add --no-cache \
  bash \
  libzip-dev \
  oniguruma-dev \
  mysql-client \
  icu-dev \
  unzip \
  zip \
  && docker-php-ext-install \
  bcmath \
  intl \
  pdo_mysql \
  zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY . .
COPY --from=vendor-builder /app/vendor ./vendor
COPY --from=frontend-builder /app/public/build ./public/build

RUN chmod +x /var/www/html/start.sh \
  && mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
  && chown -R www-data:www-data storage bootstrap/cache

ENV APP_ENV=production
ENV APP_DEBUG=false

EXPOSE 8080

CMD ["/var/www/html/start.sh"]
