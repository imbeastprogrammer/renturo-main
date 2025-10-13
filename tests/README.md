# 🧪 Testing Guide - Renturo Backend

This directory contains automated tests for the Renturo Laravel backend application.

---

## 📂 Test Structure

```
tests/
├── Feature/              # Feature/Integration tests
│   ├── Auth/
│   │   ├── ApiAuthenticationTest.php      # API login tests (OAuth2/Passport)
│   │   ├── AuthenticationTest.php         # Web login tests (Session)
│   │   ├── RegistrationTest.php          # User registration tests
│   │   ├── PasswordResetTest.php         # Password reset flow tests
│   │   ├── PasswordUpdateTest.php        # Password update tests
│   │   ├── EmailVerificationTest.php     # Email verification tests
│   │   └── PasswordConfirmationTest.php  # Password confirmation tests
│   ├── ProfileTest.php                     # User profile tests
│   └── ExampleTest.php                     # Example feature test
│
├── Unit/                 # Unit tests
│   └── ExampleTest.php                     # Example unit test
│
├── TestCase.php          # Base test case
└── CreatesApplication.php # Application bootstrap for tests
```

---

## 🚀 Running Tests

### **Run All Tests**
```bash
php artisan test
```

### **Run Specific Test File**
```bash
php artisan test tests/Feature/Auth/ApiAuthenticationTest.php
```

### **Run Specific Test Method**
```bash
php artisan test --filter test_api_login_with_valid_credentials_returns_access_token
```

### **Run Tests with Coverage**
```bash
php artisan test --coverage
```

### **Run Tests in Parallel**
```bash
php artisan test --parallel
```

---

## 🔧 Test Setup

### **Prerequisites**

1. **Install Passport** (First time only)
```bash
php artisan passport:install
```

2. **Configure Testing Database**

Update `.env.testing` or use in-memory SQLite:

```env
DB_CONNECTION=sqlite
DB_DATABASE=:memory:
```

Or use a separate MySQL test database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=renturo_test
DB_USERNAME=root
DB_PASSWORD=
```

3. **Run Migrations** (Automatic with RefreshDatabase)

Tests using `RefreshDatabase` trait will automatically migrate the database before each test.

---

## 📝 Writing Tests

### **Feature Test Example (API)**

```php
<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Passport\Passport;

class MyApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_example_api_endpoint(): void
    {
        // Create a user
        $user = User::factory()->create();

        // Simulate authenticated API request
        Passport::actingAs($user);

        // Make API request
        $response = $this->getJson('/api/v1/endpoint');

        // Assert response
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }
}
```

### **Feature Test Example (Web)**

```php
<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MyWebTest extends TestCase
{
    use RefreshDatabase;

    public function test_example_web_page(): void
    {
        // Create and authenticate user
        $user = User::factory()->create();
        $this->actingAs($user);

        // Visit page
        $response = $this->get('/admin/dashboard');

        // Assert response
        $response->assertStatus(200)
            ->assertSee('Dashboard');
    }
}
```

### **Unit Test Example**

```php
<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\MyService;

class MyServiceTest extends TestCase
{
    public function test_service_method(): void
    {
        $service = new MyService();
        
        $result = $service->doSomething('input');
        
        $this->assertEquals('expected', $result);
    }
}
```

---

## 🔑 API Authentication Testing

### **Using Passport::actingAs()**

For testing protected API endpoints, use Laravel Passport's `actingAs()` method:

```php
use Laravel\Passport\Passport;

public function test_protected_endpoint(): void
{
    $user = User::factory()->create();
    
    // Simulate authenticated API request
    Passport::actingAs($user);
    
    $response = $this->getJson('/api/v1/user');
    
    $response->assertStatus(200);
}
```

### **Testing Login Flow**

```php
public function test_login_returns_access_token(): void
{
    $user = User::factory()->create();

    $response = $this->postJson('/api/v1/login', [
        'email' => $user->email,
        'password' => 'password', // Default factory password
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'body' => [
                'access_token',
                'user',
            ],
        ]);
}
```

---

## 🧰 Available Test Traits

### **RefreshDatabase**
Automatically migrates and refreshes database before each test:

```php
use Illuminate\Foundation\Testing\RefreshDatabase;

class MyTest extends TestCase
{
    use RefreshDatabase;
}
```

### **WithFaker**
Provides access to Faker for generating fake data:

```php
use Illuminate\Foundation\Testing\WithFaker;

class MyTest extends TestCase
{
    use WithFaker;

    public function test_example(): void
    {
        $name = $this->faker->name;
        $email = $this->faker->email;
    }
}
```

### **WithoutMiddleware**
Disables all middleware for testing:

```php
use Illuminate\Foundation\Testing\WithoutMiddleware;

class MyTest extends TestCase
{
    use WithoutMiddleware;
}
```

---

## 🏭 Using Factories

### **Create a Single User**

```php
$user = User::factory()->create();
```

### **Create Multiple Users**

```php
$users = User::factory()->count(10)->create();
```

### **Create with Specific Attributes**

```php
$user = User::factory()->create([
    'email' => 'specific@example.com',
    'role' => User::ROLE_ADMIN,
]);
```

### **Create Without Persisting**

```php
$user = User::factory()->make(); // Creates but doesn't save to database
```

---

## 📊 Assertions

### **HTTP Status**

```php
$response->assertStatus(200);
$response->assertOk();
$response->assertCreated(); // 201
$response->assertNoContent(); // 204
$response->assertUnauthorized(); // 401
$response->assertForbidden(); // 403
$response->assertNotFound(); // 404
```

### **JSON Response**

```php
$response->assertJson([
    'key' => 'value',
]);

$response->assertJsonStructure([
    'data' => [
        'id',
        'name',
    ],
]);

$response->assertJsonPath('data.id', 1);
```

### **Database**

```php
$this->assertDatabaseHas('users', [
    'email' => 'test@example.com',
]);

$this->assertDatabaseMissing('users', [
    'email' => 'deleted@example.com',
]);

$this->assertDatabaseCount('users', 5);
```

### **Authentication**

```php
$this->assertAuthenticated();
$this->assertGuest();
$this->assertAuthenticatedAs($user);
```

---

## 🎯 Test Coverage

### **Current Test Coverage**

| Feature | Coverage | Notes |
|---------|----------|-------|
| **Web Authentication** | ✅ Complete | Login, registration, password reset |
| **API Authentication** | ✅ Complete | API login, logout, token validation |
| **User Profile** | ✅ Partial | Basic profile tests |
| **API Endpoints** | ⚠️ Minimal | Need tests for stores, forms, etc. |

### **Areas Needing Tests**

- [ ] Store/Listing CRUD operations
- [ ] Dynamic form submissions
- [ ] Bank account management
- [ ] Chat/messaging functionality
- [ ] Category management
- [ ] User management (admin functions)
- [ ] OTP verification flow
- [ ] Multi-tenant functionality

---

## 🚨 Common Issues

### **Issue: Passport not installed**

**Error:** `InvalidArgumentException: Client not found`

**Solution:**
```bash
php artisan passport:install
```

### **Issue: Database not migrated**

**Error:** `SQLSTATE[42S02]: Base table or view not found`

**Solution:** Use `RefreshDatabase` trait in your test class.

### **Issue: 404 Not Found for API routes**

**Problem:** Multi-tenant middleware may not work in tests

**Solution:** Mock tenant context or test without tenancy middleware.

---

## 📚 Best Practices

### **1. Use Descriptive Test Names**

```php
// ✅ Good
public function test_api_login_with_valid_credentials_returns_access_token(): void

// ❌ Bad
public function test_login(): void
```

### **2. Arrange-Act-Assert Pattern**

```php
public function test_example(): void
{
    // Arrange
    $user = User::factory()->create();
    
    // Act
    $response = $this->postJson('/api/endpoint', ['data' => 'value']);
    
    // Assert
    $response->assertStatus(200);
}
```

### **3. Test One Thing Per Test**

```php
// ✅ Good - separate tests
public function test_login_with_valid_credentials(): void { }
public function test_login_with_invalid_password(): void { }

// ❌ Bad - testing multiple scenarios
public function test_login(): void { 
    // tests both valid and invalid...
}
```

### **4. Use Factories Over Manual Creation**

```php
// ✅ Good
$user = User::factory()->create();

// ❌ Bad
$user = new User([
    'name' => 'Test',
    'email' => 'test@test.com',
    // ... many fields
]);
$user->save();
```

### **5. Clean Up After Tests**

Use `RefreshDatabase` to ensure tests don't affect each other:

```php
class MyTest extends TestCase
{
    use RefreshDatabase; // Resets database after each test
}
```

---

## 🔗 References

- **Laravel Testing Docs:** https://laravel.com/docs/testing
- **PHPUnit Docs:** https://phpunit.de/documentation.html
- **Laravel Passport Testing:** https://laravel.com/docs/passport#testing

---

## 📝 TODO: Future Tests

- [ ] Integration tests for complete user flows
- [ ] Performance tests for API endpoints
- [ ] Security tests for authentication bypass attempts
- [ ] Multi-tenant isolation tests
- [ ] File upload tests
- [ ] Email sending tests
- [ ] WebSocket/broadcasting tests
- [ ] API rate limiting tests

---

**Last Updated:** October 13, 2025  
**Test Framework:** PHPUnit 9.x  
**Laravel Version:** 9.19

