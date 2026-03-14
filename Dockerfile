FROM node:20-alpine AS frontend-builder
WORKDIR /app

COPY package*.json ./
RUN npm ci

COPY resources ./resources
COPY public ./public
COPY vite.config.js postcss.config.js tailwind.config.js ./
RUN npm run build

FROM php:8.2-cli-alpine
WORKDIR /var/www/html

ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_MEMORY_LIMIT=-1

RUN apk add --no-cache \
  bash \
  git \
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

COPY composer.json composer.lock ./
RUN composer install \
  --no-dev \
  --no-interaction \
  --no-progress \
  --prefer-dist \
  --no-ansi \
  --no-scripts

COPY . .
COPY --from=frontend-builder /app/public/build ./public/build

RUN chmod +x /var/www/html/start.sh \
  && mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
  && chown -R www-data:www-data storage bootstrap/cache

ENV APP_ENV=production
ENV APP_DEBUG=false

EXPOSE 8080

CMD ["/var/www/html/start.sh"]
