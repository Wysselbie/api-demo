# Use API Platform Base Image
FROM ghcr.io/wysselbie/apiplatform-base:php8.3-1.0.0

# Copy nginx configuration
COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf

# Copy supervisor configuration
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copy application code
COPY . /var/www/html

# Create /var/www/html/var directory
RUN mkdir -p /var/www/html/var

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/var

USER www-data

# Install composer dependencies
RUN composer install --no-dev --optimize-autoloader

# Create necessary directories
RUN mkdir -p /var/log/nginx /var/log/supervisor

# Expose port 80
EXPOSE 80

# Start supervisor (which will manage nginx and php-fpm)
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
