# Architecture Overview

## System Architecture

The Document Tracker follows a **Layered Architecture** with **Domain-Driven Design** principles, built on Laravel 11.

### High-Level Layers

```
┌─────────────────────────────────────────────────────────────────┐
│                     PRESENTATION LAYER                           │
│  ┌──────────────┐  ┌──────────────┐  ┌───────────────────────┐  │
│  │   Blade      │  │   Tailwind   │  │     Alpine.js         │  │
│  │  Templates   │  │     CSS      │  │   (Interactivity)     │  │
│  └──────────────┘  └──────────────┘  └───────────────────────┘  │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                      APPLICATION LAYER                           │
│  ┌──────────────┐  ┌──────────────┐  ┌────────────────────────┐  │
│  │ Controllers  │  │  Requests    │  │      Middleware        │  │
│  │   (HTTP)     │  │ (Validation) │  │  (Security, Auth)      │  │
│  └──────────────┘  └──────────────┘  └────────────────────────┘  │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                       DOMAIN LAYER                               │
│  ┌──────────────┐  ┌──────────────┐  ┌────────────────────────┐  │
│  │   Models     │  │  Services    │  │      Policies          │  │
│  │ (Entities)   │  │ (Business)   │  │  (Authorization)       │  │
│  └──────────────┘  └──────────────┘  └────────────────────────┘  │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                    INFRASTRUCTURE LAYER                          │
│  ┌──────────────┐  ┌──────────────┐  ┌────────────────────────┐  │
│  │  Database    │  │   Cache      │  │    External APIs       │  │
│  │  (MySQL)     │  │  (Redis)     │  │  (Google OAuth)        │  │
│  └──────────────┘  └──────────────┘  └────────────────────────┘  │
└─────────────────────────────────────────────────────────────────┘
```

## Core Components

### Models (14 Entities)

| Model | Table | Responsibility |
|-------|-------|----------------|
| `User` | `users` | Authentication, roles, profile |
| `Document` | `documents` | Core document entity |
| `DocumentTrack` | `document_tracks` | Movement history |
| `DocumentType` | `document_types` | Document categories |
| `Department` | `departments` | Organizational units |
| `Office` | `offices` | Offices within departments |
| `ArtaSetting` | `arta_settings` | Processing time rules |
| `Role` | `roles` | User roles with permissions |
| `SystemSetting` | `system_settings` | Application config |
| `SecurityLog` | `security_logs` | Security audit trail |
| `UserActivity` | `user_activities` | User action logging |
| `Message` | `messages` | Internal messaging |
| `FailedLoginAttempt` | `failed_login_attempts` | Brute force tracking |
| `PasswordHistory` | `password_histories` | Password rotation |

### Services (Business Logic)

| Service | Methods |
|---------|---------|
| `EncryptionService` | `encrypt()`, `decrypt()` |
| `SecurityAuditService` | `logFailedLogin()`, `logSuccessfulLogin()`, `logLogout()`, `logPasswordChange()`, `logModelChange()`, `detectBruteForce()`, `logSuspiciousActivity()` |
| `UserActivityService` | `log()`, `logModelActivity()`, `getRecentActivities()` |

### Controllers (20+)

| Controller | Routes | Purpose |
|------------|--------|---------|
| `Auth\LoginController` | `POST /login` | Authentication |
| `Auth\RegisterController` | `POST /register` | Registration |
| `Auth\GoogleController` | `GET /auth/google` | OAuth |
| `SystemController` | `GET /system/` | Dashboard |
| `DocumentController` | `system.documents.*` | Document CRUD + workflows |
| `UserController` | `system.users.*` | User management |
| `RoleController` | `system.roles.*` | Role management |
| `PermissionController` | `system.permissions.*` | Permission management |
| `DepartmentController` | `system.departments.*` | Department CRUD |
| `OfficeController` | `system.offices.*` | Office CRUD |
| `DocumentTypeController` | `system.document-types.*` | Document type CRUD |
| `ArtaSettingController` | `system.arta-settings.*` | ARTA config |
| `ProfileController` | `system.profile` | User profile |
| `SettingController` | `system.settings` | System settings |
| `EmailSettingController` | `system.email-settings` | Email config |
| `StatisticsController` | `system.statistics` | Analytics |
| `ActivityLogController` | `system.activity-logs` | Activity log |
| `SecurityLogController` | `system.security-logs` | Security log |
| `ChatController` | `system.messages` | Messaging |

### Middleware (Security)

| Middleware | Purpose |
|------------|---------|
| `ForceHttpsMiddleware` | Enforce HTTPS |
| `InputSanitizeMiddleware` | XSS prevention |
| `BlockIpsMiddleware` | IP blocking |
| `SecurityMonitorMiddleware` | Suspicious activity detection |
| `SecurityHeadersMiddleware` | CSP, HSTS, X-Frame-Options |
| `CheckRole` | Role-based access |

### Form Requests (Validation)

| Request | Rules |
|---------|-------|
| `LoginRequest` | email, password, captcha |
| `RegisterRequest` | name, email, password (12+ chars, complexity) |
| `ForgotPasswordRequest` | email |
| `ResetPasswordRequest` | email, password, confirmation |
| `PasswordChangeRequest` | current_password, new_password |

## Design Patterns

### 1. Repository Pattern (via Eloquent)
Models encapsulate data access logic through relationships and scopes.

### 2. Service Layer
Business logic extracted from controllers into dedicated services.

### 3. Form Request Validation
All input validation centralized in dedicated request classes.

### 4. Middleware Pipeline
Cross-cutting concerns (security, auth) handled via middleware.

### 5. Policy-Based Authorization
Fine-grained permissions via `User::hasPermission()` and policies.

### 6. Observer Pattern
Model events trigger activity logging automatically.

### 7. Factory Pattern
Test data generation via Laravel Factories.

### 6. Strategy Pattern
Different ARTA processing time calculations.

## Data Flow

### Document Lifecycle

```
┌─────────┐     ┌─────────┐     ┌────────────┐     ┌──────────┐
│ CREATE  │────▶│ PENDING │────▶│ IN_PROGRESS│────▶│ FINISHED │
└─────────┘     └─────────┘     └────────────┘     └──────────┘
                  ▲                                    │
                  │                                    │
                  ▼                                    ▼
             ┌─────────┐                         ┌──────────┐
             │TERMINATED│────────────────────────▶│ REOPENED │
             └─────────┘                          └──────────┘
```

### Document Tracking Flow

1. **Create** → Admin creates document, auto-generates QR/Barcode
2. **Receive** → Staff scans QR, system creates `DocumentTrack`
3. **Process** → Document moves between offices, each creates track
4. **Finish** → Final office marks complete, updates status
5. **Terminate** → Optional termination with reason
7. **Reopen** → Admin can reopen finished/terminated docs

## Security Architecture

### Authentication
- Laravel Sanctum session-based
- Google OAuth 2.0 support
- Password policy (12+ chars, complexity)
- Rate limiting (login, registration, reset)

### Authorization
- RBAC: Roles → Permissions → Users
- `User::hasPermission()` checks
- `@can` Blade directives
- Policy classes for resources

### Data Protection
- **PII Encryption**: Email encrypted at rest (AES-256-CBC)
- **Password Hashing**: Bcrypt with configurable rounds
- **Session Security**: Fingerprinting, secure cookies, HTTPS enforcement

### Audit & Monitoring
- **Security Logs**: All auth events, brute force, suspicious activity
- **Activity Logs**: User actions with IP, user agent, metadata
- **Model Changes**: Automatic logging of CRUD operations

### Input Security
- **Sanitization**: All inputs sanitized
- **Validation**: Server-side via Form Requests
- **CSRF Protection**: Built-in Laravel CSRF
- **Content Security Policy**: Strict CSP headers

## Database Design

### Key Relationships

```
User ──┬──► Role (many-to-many)
       ├──► Department (belongs-to)
       ├──► Office (belongs-to)
       └──► Document (has-many as creator)

Document ──► DocumentTrack (has-many)
          ├──► DocumentType (belongs-to)
          ├──► ArtaSetting (belongs-to)
          └──► User as creator (belongs-to)

DocumentTrack ──► User (belongs-to)
             └──► Document (belongs-to)

Department ──► User (has-many)
           └──► Office (has-many)

Office ──► Department (belongs-to)
        └──► User (has-many)
```

### Indexes

- `users.email` (unique)
- `users.id_number` (unique)
- `documents.qr_value` (unique)
- `documents.barcode_value` (unique)
- `document_tracks.document_id + released_at`
- `security_logs.user_id + created_at`
- `user_activities.user_id + created_at`

## Scalability Considerations

### Horizontal Scaling
- Stateless application servers
- Shared Redis for sessions/cache
- Read replicas for MySQL
- Queue workers for background jobs

### Performance Optimizations
- Eager loading for relationships
- Database indexes on foreign keys
- Cached config/routes/views in production
- Pagination for large datasets
- Chunked processing for bulk operations

### Caching Strategy
- Config/Routes/Views: Cached in production
- System Settings: 1-hour TTL
- User Permissions: Per-request cache
- Document Counts: Dashboard caching

## Deployment Architecture

```
                    ┌─────────────┐
                    │ Load Balancer │
                    └──────┬──────┘
                           │
          ┌────────────────┼────────────────┐
          ▼                ▼                ▼
      ┌─────────┐     ┌─────────┐     ┌─────────┐
      │ App 1   │     │ App 2   │     │ App N   │
      │(PHP-FPM)    │(PHP-FPM)    │(PHP-FPM)    │
      └────┬────┘     └────┬────┘     └────┬────┘
           │               │               │
           └───────────────┼───────────────┘
                           ▼
              ┌─────────────────────────┐
              │     Shared Storage      │
              │ (Redis + File Storage)  │
              └────────────┬────────────┘
                           │
          ┌────────────────┼────────────────┐
          ▼                ▼                ▼
      ┌──────────┐   ┌──────────┐    ┌──────────┐
      │ MySQL    │   │ Redis    │    │ Queue    │
      │ Primary  │   │ Cluster  │    │ Workers  │
      └──────────┘   └──────────┘    └──────────┘
```

## Technology Stack Summary

| Layer | Technology | Version |
|-------|------------|---------|
| Framework | Laravel | 13.x |
| Language | PHP | 8.4+ |
| Database | MySQL | 8.0+ |
| Frontend | Blade + Tailwind CSS | 3.x |
| JavaScript | Alpine.js | 3.x |
| Auth | Laravel Sanctum | 4.x |
| Testing | PHPUnit | 12.x |
| Code Style | Laravel Pint | 1.x |
| Static Analysis | PHPStan | 2.x |
| CI/CD | GitHub Actions | - |