#!/bin/sh
set -e

# Run database migrations
echo "Running database migrations..."
php bin/console doctrine:migrations:migrate --no-interaction

# Install assets
echo "Installing assets..."
php bin/console assets:install

# Start supervisord
echo "Starting supervisord..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
