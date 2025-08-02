# Laravel Architecture Documentation

## 📚 Overview

This document serves as a comprehensive guide to the Laravel architecture, patterns, and concepts used in the Renturo project. It's a living document that we'll update as we discover and implement new features.

---

## 🏗️ Multi-Tenant Architecture

### Core Concept
Renturo uses **Stancl Tenancy** for multi-tenancy, allowing multiple businesses to run on the same Laravel application with isolated data.

### Key Components

#### 1. Tenant Identification
```php
// Tenants are identified by domain
// Example: main.renturo.test, client.renturo.test
```

#### 2. Database Isolation
- **Central Database**: Contains tenant information and global data
- **Tenant Databases**: Each tenant has its own isolated database
- **Automatic Switching**: Laravel automatically switches to the correct tenant database based on domain

#### 3. Route Organization
```php
// Central routes (main.renturo.test)
routes/api.php
routes/web.php

// Tenant-specific routes
routes/tenants/admin.php    // admin.tenant-domain.com
routes/tenants/owner.php    // owner.tenant-domain.com  
routes/tenants/client.php   // client.tenant-domain.com
```

### Tenant Registration Process
1. **Domain Registration**: Add domain to `domains` table in central database
2. **Database Creation**: Laravel automatically creates tenant database
3. **Migration**: Run `php artisan tenants:migrate` to create tenant tables
4. **Seeding**: Run `php artisan tenants:seed` to populate tenant data

---

## 🎯 Observers Pattern

### Purpose
Observers automatically handle model events (creating, updating, deleting) to maintain data integrity and reduce repetitive code.

### Available Events
```php
creating    // Before model is created
created     // After model is created
updating    // Before model is updated
updated     // After model is updated
deleting    // Before model is deleted
deleted     // After model is deleted
restoring   // Before model is restored
restored    // After model is restored
```

### Current Observers in Renturo

#### 1. DynamicFormPageObserver
**Location**: `app/Observers/Tenants/DynamicFormPageObserver.php`

**Purpose**:
- Auto-assign `user_id` when creating form pages
- Auto-calculate `sort_no` for page ordering
- Ensure data integrity across multi-tenant environment

**Key Logic**:
```php
public function creating(DynamicFormPage $dynamicFormPage)
{
    // Auto-assign user_id if authenticated
    if (!$dynamicFormPage->user_id && Auth::check()) {
        $dynamicFormPage->user_id = Auth::user()->id;
    }

    // Auto-calculate sort order
    if (!$dynamicFormPage->sort_no && Auth::check()) {
        $maxSortNo = DynamicFormPage::where('user_id', Auth::user()->id)->max('sort_no') + 1;
        $dynamicFormPage->sort_no = $maxSortNo;
    }
}
```

#### 2. DynamicFormFieldObserver
**Location**: `app/Observers/Tenants/DynamicFormFieldObserver.php`

**Purpose**:
- Auto-assign `user_id` when creating form fields
- Auto-calculate `sort_no` for field ordering within pages
- Maintain field hierarchy and organization

**Key Logic**:
```php
public function creating(DynamicFormField $dynamicFormField)
{
    // Auto-assign user_id if authenticated
    if (!$dynamicFormField->user_id && Auth::check()) {
        $dynamicFormField->user_id = Auth::user()->id;
    }

    // Auto-calculate field order within page
    if (!$dynamicFormField->sort_no && Auth::check()) {
        $maxSortNo = DynamicFormField::where('dynamic_form_page_id', $dynamicFormField->dynamic_form_page_id)
            ->where('user_id', Auth::user()->id)->max('sort_no') + 1;
        $dynamicFormField->sort_no = $maxSortNo;
    }
}
```

### Observer Registration
**Location**: `app/Providers/EventServiceProvider.php`

```php
public function boot()
{
    DynamicFormPage::observe(DynamicFormPageObserver::class);
    DynamicFormField::observe(DynamicFormFieldObserver::class);
}
```

### Benefits
1. **Automatic Data Management**: No need to manually assign user_id or sort_no
2. **Business Logic Separation**: Keeps controllers clean and focused
3. **Data Integrity**: Ensures consistent data structure across the application
4. **Multi-Tenant Safety**: Prevents cross-tenant data contamination

---

## 🌱 Seeder System

### Architecture Overview

#### 1. Central Seeders
- **Location**: `database/seeders/`
- **Purpose**: Populate central database with global data
- **Command**: `php artisan db:seed`

#### 2. Tenant Seeders
- **Location**: `database/seeders/` (tenant-specific classes)
- **Purpose**: Populate tenant databases with business-specific data
- **Command**: `php artisan tenants:seed`

### Seeder Hierarchy

#### Master Seeders
1. **TenantFormSystemSeeder**: Orchestrates category and form creation
2. **TenantDatabaseSeeder**: Main tenant seeder orchestrator

#### Individual Seeders
1. **TenantCategorySeeder**: Creates categories and subcategories
2. **TenantDynamicFormSeeder**: Creates basic dynamic forms
3. **TenantBasketballArenaFormSeeder**: Creates detailed basketball arena form
4. **TenantPostSeeder**: Creates sample posts

### Seeder Dependencies
```php
TenantFormSystemSeeder
├── TenantCategorySeeder (runs first)
└── TenantDynamicFormSeeder (runs second)

TenantBasketballArenaFormSeeder
├── Requires TenantCategorySeeder (for Basketball subcategory)
└── Requires TenantDynamicFormSeeder (for Basketball Arena form)
```

### Best Practices
1. **Dependency Order**: Always run category seeders before form seeders
2. **Idempotent**: Use `firstOrCreate()` to prevent duplicate data
3. **Error Handling**: Check for required dependencies before seeding
4. **User Context**: Ensure user exists for user-dependent seeders

---

## 🔄 Service Providers

### Key Service Providers

#### 1. TenancyServiceProvider
**Location**: `app/Providers/TenancyServiceProvider.php`

**Purpose**:
- Registers tenant-specific routes
- Handles multi-tenant routing logic
- Maps web and API routes for different tenant types

**Key Methods**:
```php
public function mapWebRoutes()
{
    // Maps routes for different tenant types
    // admin, owner, client, user routes
}

public function mapApiRoutes()
{
    // Maps API routes for tenant-specific endpoints
}
```

#### 2. EventServiceProvider
**Location**: `app/Providers/EventServiceProvider.php`

**Purpose**:
- Registers model observers
- Handles application-wide events
- Manages event-listener relationships

#### 3. AppServiceProvider
**Location**: `app/Providers/AppServiceProvider.php`

**Purpose**:
- Application-wide service registration
- Global configuration
- Boot-time initialization

---

## 🗄️ Database Architecture

### Multi-Database Structure

#### Central Database (`renturo-new`)
**Tables**:
- `tenants`: Tenant information
- `domains`: Domain-to-tenant mapping
- `users`: Central user management
- `migrations`: Migration tracking

#### Tenant Databases (`tenant_*`)
**Tables**:
- `categories`: Business categories
- `sub_categories`: Business subcategories
- `dynamic_forms`: Form definitions
- `dynamic_form_pages`: Form page structure
- `dynamic_form_fields`: Form field definitions
- `posts`: Business listings
- `users`: Tenant-specific users

### Migration Strategy

#### Central Migrations
```bash
php artisan migrate
```

#### Tenant Migrations
```bash
php artisan tenants:migrate
php artisan tenants:migrate-fresh
```

### Seeding Strategy

#### Central Seeding
```bash
php artisan db:seed
```

#### Tenant Seeding
```bash
php artisan tenants:seed
php artisan tenants:seed --class=TenantFormSystemSeeder
```

---

## 🔐 Authentication & Authorization

### Multi-Tenant Auth
- **Central Auth**: Manages global user accounts
- **Tenant Auth**: Manages tenant-specific user accounts
- **Domain-Based**: Authentication context based on current domain

### Auth Guards
```php
// Central authentication
Auth::guard('web')->user()

// Tenant-specific authentication
Auth::guard('tenant')->user()
```

---

## 📡 API Architecture

### Route Structure
```
/api/v1/          # Central API endpoints
/api/client/v1/   # Client-specific endpoints
/api/admin/v1/    # Admin-specific endpoints
/api/owner/v1/    # Owner-specific endpoints
```

### Response Patterns
```php
// Success Response
{
    "status": "success",
    "data": {...},
    "message": "Operation completed"
}

// Error Response
{
    "status": "error",
    "message": "Error description",
    "errors": {...}
}
```

---

## 🎨 Frontend Integration

### Inertia.js Integration
- **Server-Side Rendering**: Laravel + Inertia.js
- **SPA Experience**: Single-page application feel
- **Shared State**: Server and client state synchronization

### API Integration
- **RESTful APIs**: Standard REST endpoints
- **Authentication**: Token-based authentication
- **CORS**: Cross-origin resource sharing configuration

---

## 🚀 Development Workflow

### Local Development Setup
1. **Valet**: Domain management (`main.renturo.test`)
2. **Ngrok**: External access (`renturo.ngrok.app`)
3. **Database**: Multi-tenant database setup
4. **Seeders**: Populate with test data

### Testing Strategy
1. **Unit Tests**: Individual component testing
2. **Feature Tests**: End-to-end functionality testing
3. **Tenant Tests**: Multi-tenant specific testing

---

## 🔧 Configuration Management

### Environment Variables
```env
# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=renturo-new
DB_USERNAME=root
DB_PASSWORD=

# Multi-tenancy
TENANCY_DATABASE_AUTO_CREATE=true
TENANCY_DATABASE_AUTO_UPDATE_SCHEMA=true
```

### Tenant Configuration
```php
// config/tenancy.php
'domain_model' => \App\Models\Domain::class,
'tenant_model' => \App\Models\Tenant::class,
```

---

## 📝 Best Practices

### Code Organization
1. **Single Responsibility**: Each class has one clear purpose
2. **Dependency Injection**: Use Laravel's DI container
3. **Observer Pattern**: Use observers for model events
4. **Service Classes**: Extract business logic to services

### Database Design
1. **Multi-Tenant Isolation**: Ensure data separation
2. **Indexing**: Proper database indexing
3. **Relationships**: Clear model relationships
4. **Migrations**: Version-controlled schema changes

### Security
1. **Input Validation**: Always validate user input
2. **SQL Injection**: Use Eloquent ORM
3. **XSS Protection**: Escape output
4. **CSRF Protection**: Laravel's built-in CSRF protection

---

## 🔄 Version Control Strategy

### Branching Strategy
- `main`: Production-ready code
- `develop`: Development branch
- `feature/*`: Feature development
- `hotfix/*`: Emergency fixes

### Migration Strategy
1. **Backward Compatibility**: Ensure migrations are reversible
2. **Data Preservation**: Backup before major changes
3. **Testing**: Test migrations in staging environment

---

## 📊 Monitoring & Logging

### Logging Strategy
```php
// Application logs
Log::info('User action', ['user_id' => $user->id]);

// Error logging
Log::error('Database error', ['error' => $e->getMessage()]);
```

### Performance Monitoring
1. **Database Queries**: Monitor query performance
2. **Memory Usage**: Track memory consumption
3. **Response Times**: Monitor API response times

---

## 🚀 Deployment Strategy

### Environment Setup
1. **Production**: Optimized for performance
2. **Staging**: Mirror of production
3. **Development**: Local development environment

### Deployment Process
1. **Code Deployment**: Deploy application code
2. **Database Migration**: Run pending migrations
3. **Cache Clear**: Clear application cache
4. **Health Check**: Verify deployment success

---

## 📚 Additional Resources

### Laravel Documentation
- [Laravel Official Docs](https://laravel.com/docs)
- [Stancl Tenancy](https://tenancyforlaravel.com/)
- [Inertia.js](https://inertiajs.com/)

### Development Tools
- **Laravel Telescope**: Application debugging
- **Laravel Horizon**: Queue monitoring
- **Laravel Debugbar**: Development debugging

---

## 🔄 Document Maintenance

### Update Frequency
- **Weekly**: Review and update architecture decisions
- **Monthly**: Comprehensive document review
- **Per Release**: Update with new features

### Contribution Guidelines
1. **Clear Documentation**: Write clear, concise explanations
2. **Code Examples**: Include practical code examples
3. **Version Control**: Track changes in git
4. **Review Process**: Peer review for accuracy

---

*This document is a living guide that evolves with the project. Last updated: [Current Date]* 