# Use API Platform Base Image
FROM ghcr.io/wysselbie/apiplatform-base:php8.4-1.0.0

# Copy nginx configuration
COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf

# Copy supervisor configuration
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copy entrypoint script
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Copy application code
COPY . /var/www/html

# Create /var/www/html/var directory
RUN mkdir -p /var/www/html/var

# Create nginx temporary directories and set proper permissions
RUN mkdir -p /var/lib/nginx/body /var/lib/nginx/fastcgi /var/lib/nginx/proxy /var/lib/nginx/scgi /var/lib/nginx/uwsgi \
    && chown -R www-data:www-data /var/lib/nginx \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/var

USER www-data

# Install composer dependencies
RUN composer install --no-dev --optimize-autoloader

RUN php bin/console assets:install

# Expose port 80
EXPOSE 80

ENV FPM_LISTEN=/tmp/php-fpm.sock

# Start entrypoint script (runs migrations then supervisord)
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
