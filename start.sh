#!/bin/bash
set -e

# Render injects a PORT env variable; configure Apache to listen on it
APP_PORT=${PORT:-80}

echo "Configuring Apache on port $APP_PORT..."
sed -i "s/Listen 80/Listen $APP_PORT/" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost *:$APP_PORT>/" /etc/apache2/sites-available/000-default.conf

# APP_KEY is already baked into the image via Dockerfile.
# If Render provides APP_KEY as an env var, it overrides the .env value automatically.

# Show DB config for debugging (no password)
echo "DB config => connection=${DB_CONNECTION} host=${DB_HOST} port=${DB_PORT} database=${DB_DATABASE} user=${DB_USERNAME}"

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
