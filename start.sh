#!/bin/sh

# Cache configs and routes
php artisan config:cache
php artisan route:cache

# Run migrations safely in production
php artisan migrate --force

# Start Laravel server
php artisan serve --host=0.0.0.0 --port=8080