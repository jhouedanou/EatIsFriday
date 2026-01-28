# EatIsFamily WordPress with Duplicator Pro support
# Using PHP 8.1 for better Duplicator Pro compatibility
FROM wordpress:php8.1

# Install required PHP extensions for Duplicator Pro
RUN apt-get update && apt-get install -y \
    libzip-dev \
    && docker-php-ext-install zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Create Duplicator backup directory with proper permissions
RUN mkdir -p /var/www/html/wp-content/backups-dup-pro/tmp \
    && mkdir -p /var/www/html/wp-content/backups-dup-pro/logs \
    && chown -R www-data:www-data /var/www/html/wp-content/backups-dup-pro \
    && chmod -R 755 /var/www/html/wp-content/backups-dup-pro
