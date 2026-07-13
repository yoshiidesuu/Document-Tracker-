# Installation Guide

## System Requirements

- **PHP**: 8.4 or higher
- **Composer**: 2.0+
- **MySQL**: 8.0 or higher
- **Node.js**: 20+ and npm
- **Git**: 2.0+

## Step-by-Step Installation

### 1. Clone Repository

```bash
git clone https://github.com/yoshiidesuu/Document-Tracker-.git
cd Document-Tracker-
```

### 2. Install PHP Dependencies

```bash
composer install --no-interaction --prefer-dist --optimize-autoloader
```

### 3. Install Node Dependencies

```bash
npm install
```

### 4. Configure Environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` with your configuration:

```env
APP_NAME="Document Tracker"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=document-tracker
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 5. Database Setup

```bash
# Create database
mysql -u root -p -e "CREATE DATABASE document_tracker CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Run migrations
php artisan migrate --force
```

### 6. Build Assets

```bash
# Development
npm run dev

# Production
npm run build
```

### 7. Start Application

```bash
php artisan serve
```

Visit `http://localhost:8000`

## Production Deployment

### Server Requirements

- Ubuntu 22.04+ / CentOS 8+
- Nginx or Apache
- PHP-FPM 8.4
- MySQL 8.0
- Redis (optional, for queues/cache)
- SSL Certificate (Let's Encrypt recommended)

### Nginx Configuration

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/document-tracker/public;

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### Production Optimizations

```bash
# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimize autoloader
composer install --optimize-autoloader --no-dev

# Build production assets
npm run build
```

### Queue Workers

```bash
# Supervisor configuration for queue workers
[program:document-tracker-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/document-tracker/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/document-tracker/storage/logs/worker.log
```

### Scheduler

```bash
# Add to crontab
* * * * * cd /var/www/document-tracker && php artisan schedule:run >> /dev/null 2>&1
```

## Testing Installation

```bash
# Run migrations
php artisan migrate --force

# Run tests
php artisan test

# Check code style
./vendor/bin/pint --test

# Static analysis
./vendor/bin/phpstan analyse --level=5

# Security audit
composer audit
```

## Troubleshooting

### Permission Issues

```bash
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

### Key Generation Error

```bash
php artisan key:generate --force
```

### Migration Errors

```bash
php artisan migrate:fresh --force
```

### Asset Compilation Errors

```bash
npm ci
npm run build
```

## Docker Installation (Optional)

```dockerfile
# Dockerfile
FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
COPY . .
RUN composer install --optimize-autoloader --no-dev

EXPOSE 9000
CMD ["php-fpm"]
```

```bash
# Build and run
docker build -t document-tracker .
docker run -d -p 8000:9000 document-tracker
```