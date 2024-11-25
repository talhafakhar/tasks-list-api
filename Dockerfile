# Use the official PHP image with Apache and PHP 8.2
FROM php:8.2-apache

# Set working directory
WORKDIR /var/www/html

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    postgresql-client \
    redis-tools \
    libjpeg-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    libzip-dev \
    libonig-dev \
    libmcrypt-dev \
    libpq-dev \
    libxml2-dev \
    libssl-dev \
    libcurl4-openssl-dev \
    nodejs \
    npm \
    pkg-config && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install pdo pdo_mysql gd exif pcntl bcmath opcache zip pdo_pgsql && \
    pecl install redis && \
    docker-php-ext-enable redis

# Add Node.js 20
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && \
    apt-get install -y nodejs


# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy virtual host configuration file
COPY apache/vhosts.conf /etc/apache2/sites-available/000-default.conf
COPY apache/ports.conf /etc/apache2/ports.conf

# Enable Apache mod_rewrite and vhost_alias
RUN a2enmod rewrite
RUN a2enmod vhost_alias

# Copy existing application directory contents (before dependencies)
COPY . /var/www/html

# Change the owner of the Laravel storage and cache directories to www-data
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Install PHP dependencies (after copying application code)
RUN composer install --no-dev --optimize-autoloader

# Copy environment file
COPY .env.example .env

# Expose port 80 and 8080 and start Apache
EXPOSE 80 8080

# Add build arguments
ARG REACT_APP_API_URL

WORKDIR /var/www/html/resources/frontend
RUN npm install
RUN REACT_APP_API_URL=$REACT_APP_API_URL npm run build

WORKDIR /var/www/html
# Copy the entrypoint script
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Set the entrypoint
ENTRYPOINT ["entrypoint.sh"]

CMD ["apache2-foreground"]

