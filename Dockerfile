FROM php:8.3-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    nodejs \
    npm

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql gd zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy project files
COPY . .

# Install application dependencies
RUN composer install --no-interaction --prefer-dist --optimize-autoloader
RUN npm install --ignore-scripts
RUN npm run build

# Default Render port
ENV PORT=10000
EXPOSE $PORT

# Start application
CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=$PORT
