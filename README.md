# Document Tracker

A professional Laravel web application for document tracking and management with comprehensive Software Quality Assurance using Continuous Integration (CI).

## Features

- **Document Management**: Create, track, receive, finish, terminate, and reopen documents
- **ARTA Compliance**: Automatic processing time calculation based on ARTA categories (Simple, Complex, Highly Technical)
- **QR Code & Barcode Generation**: Automatic generation for document tracking
- **Role-Based Access Control (RBAC)**: Granular permissions system
- **User Management**: Complete user lifecycle with ban/lock/force logout
- **Department & Office Management**: Organizational structure
- **Document Types & ARTA Settings**: Configurable document categories and processing times
- **Activity & Security Logging**: Comprehensive audit trails
- **Real-time Notifications**: In-app messaging system
- **Statistics Dashboard**: Visual analytics and reporting

## Tech Stack

- **Framework**: Laravel 11
- **Language**: PHP 8.4
- **Database**: MySQL 8.0
- **Frontend**: Blade + Tailwind CSS
- **Testing**: PHPUnit
- **Code Style**: Laravel Pint
- **Static Analysis**: PHPStan Level 5
- **Security Audit**: Composer Audit
- **CI/CD**: GitHub Actions

## Quick Start

### Prerequisites

- PHP 8.4+
- Composer
- MySQL 8.0+
- Node.js 20+ & npm

### Installation

```bash
# Clone the repository
git clone https://github.com/yoshiidesuu/Document-Tracker-.git
cd Document-Tracker-

# Install PHP dependencies
composer install

# Install Node dependencies
npm install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure database in .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=document-tracker
# DB_USERNAME=root
# DB_PASSWORD=your_password

# Run migrations
php artisan migrate --force

# Build frontend assets
npm run build

# Start development server
php artisan serve
```

### Testing

```bash
# Run all tests
php artisan test

# Run with coverage
php artisan test --coverage

# Run specific test suite
php artisan test --filter=DocumentManagementTest
```

### Code Quality

```bash
# Check code style
./vendor/bin/pint --test

# Fix code style
./vendor/bin/pint

# Static analysis
./vendor/bin/phpstan analyse --level=5

# Security audit
composer audit
```

## CI/CD Pipeline

The project uses GitHub Actions for continuous integration:

- **Runs on**: Push and Pull requests
- **Jobs**: Test, Code Style (Laravel Pint), Static Analysis (PHPStan), Security Audit (Composer Audit)
- **Database**: MySQL 8.0 service container

### Pipeline Stages

1. **Checkout** - Clone repository
2. **Setup PHP 8.4** - Configure PHP with required extensions
3. **Cache Dependencies** - Cache Composer packages
4. **Install Dependencies** - Run `composer install`
5. **Environment Setup** - Copy `.env.testing`, generate app key
6. **Database Migrations** - Run migrations on MySQL
7. **Run Tests** - Execute PHPUnit test suite
8. **Code Style Check** - Run Laravel Pint
9. **Static Analysis** - Run PHPStan Level 5
10. **Security Audit** - Run Composer Audit

## Project Structure

```
├── app/
│   ├── Http/
│   │   ├── Controllers/        # Application controllers
│   │   ├── Middleware/         # Custom middleware
│   │   └── Requests/           # Form request validation
│   ├── Models/                 # Eloquent models
│   ├── Services/               # Business logic services
│   ├── Traits/                 # Reusable traits
│   └── Rules/                  # Custom validation rules
├── database/
│   ├── factories/              # Model factories
│   ├── migrations/             # Database migrations
│   └── seeders/                # Database seeders
├── resources/
│   ├── views/                  # Blade templates
│   ├── css/                    # Tailwind CSS
│   └── js/                     # JavaScript assets
├── routes/
│   ├── web.php                 # Web routes
│   ├── api.php                 # API routes
│   └── console.php             # Console commands
├── tests/
│   ├── Feature/                # Feature tests
│   └── Unit/                   # Unit tests
├── .github/
│   └── workflows/
│       └── ci.yml              # CI/CD pipeline
└── config/                     # Configuration files
```

## Key Models

- **User** - Authentication, roles, departments, offices
- **Document** - Core document entity with tracking
- **DocumentTrack** - Document movement history
- **DocumentType** - Document categories
- **Department** - Organizational departments
- **Office** - Offices within departments
- **ArtaSetting** - ARTA processing time configuration
- **Role** - User roles with permissions
- **SystemSetting** - Application configuration
- **SecurityLog** - Security event logging
- **UserActivity** - User activity tracking

## Security Features

- **Authentication**: Laravel Sanctum + Session
- **Password Policy**: 12+ chars, uppercase, lowercase, number, symbol
- **Rate Limiting**: Login, registration, password reset
- **Session Security**: Fingerprinting, secure cookies, HTTPS enforcement
- **Audit Logging**: All security events tracked
- **Input Sanitization**: XSS prevention
- **Security Headers**: CSP, HSTS, X-Frame-Options
- **Brute Force Protection**: IP-based locking

## License

MIT License - see [LICENSE](LICENSE) for details.

## CI/CD Status

![CI/CD Pipeline](https://github.com/yoshiidesuu/Document-Tracker-/actions/workflows/ci.yml/badge.svg)