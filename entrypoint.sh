#!/bin/sh

# Exit script on any error
set -e

# Wait for database to be ready
sleep 5

# Run migrations (only if they haven't been run)
php artisan migrate --force

# Start PHP-FPM
exec php-fpm