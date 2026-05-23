#!/bin/bash
set -e

# Render injects a PORT env variable; configure Apache to listen on it
APP_PORT=${PORT:-80}

echo "Configuring Apache on port $APP_PORT..."
sed -i "s/Listen 80/Listen $APP_PORT/" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost *:$APP_PORT>/" /etc/apache2/sites-available/000-default.conf

# Generate APP_KEY if not provided via environment variable
if [ -z "$APP_KEY" ]; then
    echo "Generating application key..."
    php artisan key:generate --force
fi

# Wait for DB and run migrations with retry
echo "Running database migrations..."
RETRIES=5
until php artisan migrate --force 2>&1; do
    RETRIES=$((RETRIES - 1))
    if [ "$RETRIES" -le 0 ]; then
        echo "Database migration failed after multiple attempts. Exiting."
        exit 1
    fi
    echo "Migration failed. Retrying in 5 seconds... ($RETRIES attempts left)"
    sleep 5
done

# Optimise Laravel for production
echo "Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Create storage symlink (safe to run multiple times)
php artisan storage:link --force 2>/dev/null || true

echo "Starting Apache..."
exec apache2-foreground
