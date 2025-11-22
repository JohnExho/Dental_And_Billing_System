# Base image: PHP for Laravel
FROM php:8.2-fpm

# Set working directory
WORKDIR /var/www/html

# -------------------------------
# 1️⃣ Install system dependencies
# -------------------------------
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    curl \
    python3 \
    python3-pip \
    python3-venv \
    npm \
    zlib1g-dev \
    libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip


# -------------------------------
# 2️⃣ Install Composer
# -------------------------------
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# -------------------------------
# 3️⃣ Copy project files
# -------------------------------
COPY . /var/www/html

# -------------------------------
# 4️⃣ Laravel dependencies
# -------------------------------
RUN composer install --no-dev --optimize-autoloader

# -------------------------------
# 5️⃣ Node dependencies (Laravel Mix)
# -------------------------------
RUN npm install
RUN npm run build

# -------------------------------
# 6️⃣ Python dependencies
# -------------------------------
WORKDIR /var/www/html/python

# Copy requirements
COPY python/requirements.txt .

# Create a virtual environment
RUN python3 -m venv venv

# Activate venv and install packages
RUN . venv/bin/activate && pip install --upgrade pip && pip install -r requirements.txt

# -------------------------------
# 7️⃣ Permissions
# -------------------------------
WORKDIR /var/www/html
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# -------------------------------
# 8️⃣ Expose ports
# -------------------------------
# 8000 for Laravel, 8001 for FastAPI
EXPOSE 8000 5000

# -------------------------------
# 9️⃣ Start services
# -------------------------------
# Use a simple script to start both Laravel and FastAPI
CMD bash -c "php artisan serve --host=0.0.0.0 --port=8000 & /var/www/html/python/venv/bin/uvicorn python.main:app --host 0.0.0.0 --port 5000"

