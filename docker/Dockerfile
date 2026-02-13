FROM php:8.4-apache

# 1. Apache Configuration
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf \
    && a2enmod rewrite

# 2. Base installations
RUN apt-get update && apt-get install -y \
    git curl libzip-dev libpng-dev libjpeg-dev libfreetype6-dev \
    libxml2-dev libonig-dev libcurl4-openssl-dev unzip \
    libicu-dev libpcre2-dev $PHPIZE_DEPS

# 3. PHP Extensions (AJOUT DE PCNTL)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
    pdo pdo_mysql zip gd mbstring xml curl bcmath intl pcntl

# 4. Swoole, Redis et OPcache
RUN pecl install swoole redis \
    && docker-php-ext-enable swoole redis opcache

# 5. Node.js 20
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# 6. Composer
RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin --filename=composer

# 7. Clean
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www

# 8. Copier les fichiers de dépendances depuis la racine du contexte
COPY project/composer.json project/composer.lock ./

# 9. Installer les dépendances
RUN composer install --no-scripts --no-autoloader

# 10. Copier tout le dossier project
COPY project/ .

# 11. Finir l'autoloader et permissions
RUN composer dump-autoload --optimize \
    && chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# 12. Octane Configuration
EXPOSE 8000

# Start Octane avec Swoole
CMD ["php", "artisan", "octane:start", "--server=swoole", "--host=0.0.0.0", "--port=8741"]
