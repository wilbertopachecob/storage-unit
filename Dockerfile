# Use official PHP 8.4 with Apache
FROM php:8.4-apache

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    fileinfo

# Enable Apache modules
RUN a2enmod rewrite headers

# Disable default site and enable our custom configuration
RUN a2dissite 000-default

# Copy Apache configuration
COPY docker/apache-config.conf /etc/apache2/sites-available/000-default.conf

# Enable our custom site
RUN a2ensite 000-default

# Override the default DocumentRoot in the main Apache configuration
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' /etc/apache2/apache2.conf || true

# Create a custom Apache configuration to override DocumentRoot
RUN echo "DocumentRoot /var/www/html/public" > /etc/apache2/conf-available/custom-documentroot.conf && \
    a2enconf custom-documentroot

# Copy PHP session configuration
COPY docker/php-session.ini /usr/local/etc/php/conf.d/session.ini

# Copy application files
COPY . /var/www/html/

# Create .htaccess file for additional security
RUN echo "Options -Indexes" > /var/www/html/.htaccess && \
    echo "Options -Indexes" > /var/www/html/public/.htaccess

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 777 /var/www/html/public/uploads

# Create uploads directory and session directory if they don't exist
RUN mkdir -p /var/www/html/public/uploads \
    && mkdir -p /tmp/php_sessions \
    && chown -R www-data:www-data /tmp/php_sessions \
    && chmod -R 755 /tmp/php_sessions

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
