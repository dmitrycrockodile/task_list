#!/bin/bash

# Exit script on any error
set -e

# Wait for database to be ready
sleep 5

# Fix permissions for storage and cache
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Run migrations (only if they haven't been run)
php artisan migrate --force

# Start PHP-FPM
exec php-fpm