# CI/CD Pipeline Explanation

## Overview

The CI/CD pipeline automates the verification of every code change, ensuring software quality through automated testing, code style enforcement, static analysis, and security auditing.

## Pipeline Architecture

### Workflow File
**Location**: `.github/workflows/ci.yml`

### Trigger Events
```yaml
on:
  push:
    branches: [main]
  pull_request:
    branches: [main]
```

### Job Dependency Graph

```
┌─────────────┐
│   Push/PR   │
└──────┬──────┘
       │
       ▼
┌─────────────────────────────────────────────────┐
│              Parallel Jobs                       │
├────────────┬─────────────┬────────────┬──────────┤
│   Test     │   Pint      │  PHPStan   │  Audit   │
│  (38s)     │   (11s)     │   (9s)     │  (16s)   │
└────────────┴─────────────┴────────────┴──────────┘
```

All jobs run in parallel for faster feedback.

## Job Details

### 1. Test Job (`test`)

**Purpose**: Execute full test suite against MySQL database

**Runner**: `ubuntu-latest`

**Services**:
```yaml
services:
  mysql:
    image: mysql:8.0
    env:
      MYSQL_ROOT_PASSWORD: 041601
      MYSQL_DATABASE: document_tracker_test
    ports: [3306:3306]
    options: >-
      --health-cmd="mysqladmin ping"
      --health-interval=10s
      --health-timeout=5s
      --health-retries=3
```

**Steps**:

| Step | Command | Purpose |
|------|---------|---------|
| Checkout | `actions/checkout@v4` | Clone repository |
| Setup PHP | `shivammathur/setup-php@v2` | PHP 8.4 + extensions |
| Cache Composer | `actions/cache@v3` | Cache vendor by lock hash |
| Install Deps | `composer install --prefer-dist --no-progress --no-interaction` | Install packages |
| Copy Env | `cp .env.testing .env` | Test environment |
| Generate Key | `php artisan key:generate --force` | App encryption key |
| Run Migrations | `php artisan migrate --force --env=testing` | Database schema |
| Run Tests | `php artisan test --env=testing` | Execute test suite |

**Environment**:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=document_tracker_test
DB_USERNAME=root
DB_PASSWORD=041601
CACHE_STORE=array
SESSION_DRIVER=array
QUEUE_CONNECTION=sync
MAIL_MAILER=array
```

### 2. Code Style Job (`pint`)

**Purpose**: Enforce Laravel Pint code style standards

**Steps**:
1. Checkout
2. Setup PHP 8.4
3. Cache Composer
4. Install dependencies
5. Run `./vendor/bin/pint --test`

**Failure**: Any code style violation fails the job

**Local Fix**: `./vendor/bin/pint`

### 3. Static Analysis Job (`phpstan`)

**Purpose**: Detect bugs without executing code

**Configuration**: Level 5 (strict)

**Steps**:
1. Checkout
2. Setup PHP 8.4
3. Cache Composer
4. Install dependencies
5. Run `./vendor/bin/phpstan analyse --level=5`

**Checks**:
- Type safety
- Dead code detection
- Unused variables
- Correct method signatures
- PHPDoc accuracy
- Generic type handling

### 4. Security Audit Job (`audit`)

**Purpose**: Check for known vulnerabilities in dependencies

**Steps**:
1. Checkout
2. Setup PHP 8.4
3. Cache Composer
4. Install dependencies
5. Run `composer audit --no-interaction`

**Failure**: Any security advisory fails the job

**Advisories Checked**:
- CVE vulnerabilities
- Abandoned packages
- Version constraints

## Caching Strategy

### Composer Cache
```yaml
- uses: actions/cache@v3
  with:
    path: vendor
    key: ${{ runner.os }}-php-${{ hashFiles('**/composer.lock') }}
    restore-keys: |
      ${{ runner.os }}-php-
```

**Benefits**:
- ~80% faster dependency installation
- Cache invalidated on `composer.lock` change
- Shared across all jobs

### Cache Key Strategy
```
Key: Linux-php-<hash_of_composer.lock>
Restore: Linux-php-
```

## PHP Version Strategy

### Why PHP 8.4?

The `composer.lock` contains packages requiring PHP 8.4:
- `endroid/qr-code` 6.1.3 → requires PHP ^8.4
- Symfony components v8.1+ → require PHP >=8.4.1

**Workflow Configuration**:
```yaml
php-version: '8.4'
```

## MySQL Service Configuration

### Health Checks
```yaml
options: >-
  --health-cmd="mysqladmin ping"
  --health-interval=10s
  --health-timeout=5s
  --health-retries=3
```

**Purpose**: Ensure MySQL is ready before tests run

### Connection Details
- **Host**: `127.0.0.1` (localhost in runner)
- **Port**: `3306`
- **Database**: `document_tracker_test`
- **User**: `root`
- **Password**: `041601`

## Parallel Execution Benefits

### Time Savings
| Sequential | Parallel |
|------------|----------|
| ~74s | ~42s |

### Resource Utilization
- 4 concurrent runners
- Isolated environments
- Independent failure domains

## Failure Handling

### Test Failures
- Shows failed test names
- Displays assertion messages
- Includes stack traces

### Style Failures
```
Error: app/Http/Controllers/ExampleController.php
  Line 45: concat_space
  Line 48: braces_position
```
Run `./vendor/bin/pint` locally to fix.

### Static Analysis Failures
```
Error: app/Models/User.php:45
  Method getFullNameAttribute() should return string
```
Run `./vendor/bin/phpstan analyse` locally.

### Audit Failures
```
Package symfony/console has vulnerability CVE-2024-XXXX
Upgrade to v8.1.1+
```
Run `composer update` to resolve.

## Best Practices Implemented

### 1. Fail Fast
```yaml
# Jobs stop on first failure within job
continue-on-error: false  # default
```

### 2. Cache Aggressively
- Composer packages cached
- Key includes lock file hash
- Restore keys for partial matches

### 3. Pin Action Versions
```yaml
uses: actions/checkout@v4  # not @latest
uses: shivammathur/setup-php@v2
uses: actions/cache@v3
```

### 4. Minimal Permissions
```yaml
permissions:
  contents: read  # only need to read code
```

### 5. Clear Job Names
```yaml
name: Run Tests
name: Code Style (Laravel Pint)
name: Static Analysis (PHPStan)
name: Security Audit (Composer Audit)
```

### 6. Explain Every Step
```yaml
- name: Run database migrations
  run: php artisan migrate --force --env=testing
  # Creates test database schema
```

## Monitoring & Visibility

### GitHub Actions UI
- **Actions Tab**: All workflow runs
- **Job Logs**: Expandable step details
- **Annotations**: Inline code error highlighting
- **Artifacts**: Downloadable files (future)

### Status Badge
```markdown
![CI/CD Pipeline](https://github.com/owner/repo/actions/workflows/ci.yml/badge.svg)
```

### Notifications
Configure in GitHub:
- Settings → Notifications → Actions
- Email on failure
- Slack/Discord webhooks via Actions

## Local Development Workflow

### Pre-Commit Checks
```bash
# Before commit
./vendor/bin/pint              # Fix style
./vendor/bin/phpstan analyse   # Check static analysis
php artisan test               # Run tests
composer audit                 # Security check
```

### CI Verification
```bash
# Push to trigger CI
git push origin feature-branch

# Check GitHub Actions tab
# Fix any failures
# Push fixes
```

## Troubleshooting

### MySQL Not Ready
```yaml
# Add explicit wait
- name: Wait for MySQL
  run: |
    for i in {1..30}; do
      mysqladmin ping -h 127.0.0.1 -P 3306 -u root -p041601 && break
      sleep 2
    done
```

### Composer Install Slow
```yaml
# Use prefer-dist and no-progress
run: composer install --prefer-dist --no-progress --no-interaction
```

### PHP Version Mismatch
- Ensure workflow PHP version matches `composer.json` requirements
- Use `php-version: '8.4'` for PHP 8.4 requirements

### Test Database Errors
```bash
# Ensure test database exists
mysql -u root -p041601 -e "CREATE DATABASE IF NOT EXISTS document_tracker_test;"
```

## Future Improvements

### 1. Matrix Testing
```yaml
strategy:
  matrix:
    php-version: ['8.3', '8.4']
    laravel-version: ['12.x', '13.x']
```

### 2. Test Parallelization
```yaml
# Split test suite
- name: Run Feature Tests
  run: php artisan test --testsuite=Feature
- name: Run Unit Tests
  run: php artisan test --testsuite=Unit
```

### 3. Coverage Reporting
```yaml
- name: Upload Coverage
  uses: actions/upload-artifact@v3
  with:
    name: coverage-report
    path: coverage-report/
```

### 4. Deploy on Success
```yaml
deploy:
  needs: [test, pint, phpstan, audit]
  if: github.ref == 'refs/heads/main'
  runs-on: ubuntu-latest
  steps:
    - name: Deploy to Production
      run: ./deploy.sh
```

### 5. Dependency Updates
```yaml
# Weekly scheduled run
on:
  schedule:
    - cron: '0 0 * * 0'  # Weekly
```

## Metrics & Reporting

### Pipeline Metrics
| Metric | Target |
|--------|--------|
| Pipeline Duration | < 2 minutes |
| Test Duration | < 40 seconds |
| Cache Hit Rate | > 80% |
| Failure Rate | < 5% |

### Quality Gates
| Gate | Threshold |
|------|-----------|
| Test Pass Rate | 100% |
| Code Style | 0 violations |
| Static Analysis | 0 errors |
| Security Audit | 0 vulnerabilities |

## Conclusion

The CI/CD pipeline provides:
1. **Automated Verification** - Every change validated
2. **Fast Feedback** - Parallel jobs, ~42s total
3. **Quality Gates** - Style, analysis, security
4. **Visibility** - Clear status, detailed logs
5. **Reliability** - Consistent environment, cached deps

The pipeline ensures the Document Tracker maintains high quality standards throughout development.