# Moodle 5 Development Container
# Based on official Moodle PHP Apache images with enhancements

ARG PHP_VERSION=8.3
FROM moodlehq/moodle-php-apache:${PHP_VERSION}

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libxml2-dev \
    libicu-dev \
    libldap2-dev \
    libpq-dev \
    ghostscript \
    clamav \
    graphviz \
    aspell \
    # For Node.js builds
    python3 \
    make \
    g++ \
    # Utilities
    nano \
    vim \
    curl \
    wget \
    # For better terminal experience
    sudo \
    bash-completion \
    && rm -rf /var/lib/apt/lists/*

# Configure PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        gd \
        intl \
        ldap \
        mysqli \
        opcache \
        pdo \
        pdo_mysql \
        pdo_pgsql \
        pgsql \
        soap \
        zip \
        exif

# # Install and configure Xdebug 3.x for PHP debugging
# RUN pecl install xdebug-3.3.1 \
#     && docker-php-ext-enable xdebug

# Configure Xdebug
RUN echo "xdebug.mode=debug,develop" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_port=9003" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.log=/tmp/xdebug.log" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# PHP configuration for Moodle development
RUN { \
    echo 'memory_limit=512M'; \
    echo 'max_execution_time=300'; \
    echo 'post_max_size=100M'; \
    echo 'upload_max_filesize=100M'; \
    echo 'max_input_vars=5000'; \
    echo 'display_errors=On'; \
    echo 'error_reporting=E_ALL'; \
    echo 'date.timezone=UTC'; \
    echo 'opcache.enable=1'; \
    echo 'opcache.memory_consumption=256'; \
    echo 'opcache.max_accelerated_files=20000'; \
} > /usr/local/etc/php/conf.d/moodle.ini

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

# Apache configuration for Moodle
RUN a2enmod rewrite expires headers ssl

# Copy custom Apache configuration
COPY apache-moodle.conf /etc/apache2/sites-available/000-default.conf

# Create Moodle data directories with proper permissions
RUN mkdir -p /var/www/moodledata /var/www/phpunitdata /var/www/behatdata /var/www/behatfaildumps \
    && chown -R www-data:www-data /var/www/moodledata /var/www/phpunitdata /var/www/behatdata /var/www/behatfaildumps \
    && chmod -R 0777 /var/www/moodledata /var/www/phpunitdata /var/www/behatdata /var/www/behatfaildumps

# Set working directory
WORKDIR /workspace

# Expose port 80
EXPOSE 80

# Note: User will be set by devcontainer.json remoteUser setting
# This allows features to install correctly during build

# Default command (will be overridden by docker-compose)
CMD ["apache2-foreground"]
