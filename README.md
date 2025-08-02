# Renturo - Multi-Tenant Business Platform

This README file provides comprehensive instructions on how to set up and run the Renturo project on your local machine.

## Tech Stack

**Backend:** PHP, Laravel 9.x, Multi-Tenant Architecture
**Frontend:** React JS, TypeScript, Tailwind CSS, Inertia.js
**Mobile:** Flutter (Client App)
**Database:** MySQL with Multi-Tenant Support
**Queue System:** Laravel Queues with Database Driver

## Prerequisites

Before you begin, ensure that you have the following installed on your system:

1. **PHP:** Laravel 9.x requires PHP to run. You'll need PHP version 8.0 or higher. Server Requirements (https://laravel.com/docs/9.x/deployment#server-requirements):

2. **Composer:** Composer is a PHP package manager that Laravel uses for dependency management.

3. **Database:** For storing your application's data, a database server like MySQL, PostgreSQL, or SQLite is necessary. In this project, MySQL is the chosen database system.

4. **Web Server:** You can use Apache or Nginx as your web server to serve the Laravel application.

## Configuration

1. **Environment File:** Duplicate the **.env.example** file in your project's root directory and rename it to **.env**. Update the necessary configuration options like database connection settings.

2. **Generate Application Key** In your terminal, run the following command to generate an application key:

```bash
cd /to/your-laravel-project

php artisan key:generate
```

3. **Database Setup:** Create a database for your project and update the **.env** file with the appropriate database credentials.

## Installing Dependencies

1. In your terminal, navigate to the project directory:

```bash
cd your-laravel-project
```

2. Run the following command to install project dependencies using Composer:

```bash
composer install
```

## Database Setup & Seeding

### Central Database Setup
1. To create the necessary tables in the central database with seeder, run the migrations using the following command:

```bash
php artisan migrate --seed
```

### Tenant Database Setup
2. To seed the tenant's database with categories, subcategories, and dynamic forms, run the following command:

```bash
php artisan tenants:seed
```

### Individual Seeders
You can also run specific seeders individually:

```bash
# Run all tenant seeders
php artisan tenants:seed

# Run just the form system (categories + dynamic forms)
php artisan tenants:seed --class=TenantFormSystemSeeder

# Run categories and subcategories only
php artisan tenants:seed --class=TenantCategorySeeder

# Run dynamic forms only
php artisan tenants:seed --class=TenantDynamicFormSeeder

# Run basketball arena form only
php artisan tenants:seed --class=TenantBasketballArenaFormSeeder

# Run posts seeder only
php artisan tenants:seed --class=TenantPostSeeder
```

### Fresh Start (Complete Reset)
To completely reset and recreate all tenant databases with fresh data:

```bash
php artisan tenants:migrate-fresh --seed
```

## Seeder System Overview

### What Gets Created

#### Categories & Subcategories (TenantCategorySeeder)
- **10 Main Categories**: Sports & Recreation, Food & Dining, Beauty & Wellness, Health & Fitness, Events & Entertainment, Professional Services, Automotive, Home & Garden, Education, Travel & Tourism
- **100+ Subcategories**: Each category has 10 specific subcategories (e.g., Basketball, Tennis, Swimming under Sports)

#### Dynamic Forms (TenantDynamicFormSeeder)
- **10 Pre-built Forms**: Basketball Arena, Restaurant Reservation, Salon Appointment, Gym Membership, Event Venue Booking, Tennis Court Booking, Swimming Pool, Spa Treatment, Catering Service, Conference Room
- **Proper Relationships**: Each form is linked to its appropriate subcategory

#### Basketball Arena Form (TenantBasketballArenaFormSeeder)
- **Complete Form Structure**: 6 pages with 18 fields
- **Pages**: Contact Information, Booking Details, Court & Activity, Equipment & Services, Additional Information, Agreement
- **Field Types**: Text, Email, Date, Time, Number, Select, Checkbox, Radio, Textarea, Attachment, Rating
- **Business Logic**: Complete basketball arena booking workflow

#### Posts (TenantPostSeeder)
- **Sample Posts**: Creates sample business listings and posts for testing

### Seeder Dependencies
```
TenantDatabaseSeeder
├── TenantFormSystemSeeder
│   ├── TenantCategorySeeder (creates categories & subcategories)
│   └── TenantDynamicFormSeeder (creates dynamic forms)
├── TenantBasketballArenaFormSeeder (creates complete basketball form)
└── TenantPostSeeder (creates sample posts)
```

## Running the Application

### Backend (Laravel)
1. In your terminal, navigate to the project directory if you're not already there.

2. Use the following command to start the local development server:

```bash
php artisan serve
```

### Frontend (React/Vite)
3. In a separate terminal, start the frontend development server:

```bash
npm run dev
```

### Mobile App (Flutter)
4. Navigate to the client directory and run the Flutter app:

```bash
cd ../client
flutter run --flavor dev -t lib/main_dev.dart
```

## Running Laravel Queues (Do not forget this one)

1. **Set Up the Queue Connection:** Open your **.env** file and ensure that the QUEUE_CONNECTION variable is set to the desired queue driver. In this project, **database** is the chosen connection.

`QUEUE_CONNECTION=database`

2. **Setup Sandbox Mailer Credentials:** I utilize mailtrap.io as a sandbox mailer for previewing notification emails meant for users. You have the option to either register on the platform or directly use the credentials provided, incorporating them into your environment configuration file.

**For mailtrap.io credentials:**
<br>
`email: hijaxiv540@trazeco.com`
<br>
`password: password`

**For ENV credentials**
<br>
`MAIL_MAILER=smtp`
<br>
`MAIL_HOST=sandbox.smtp.mailtrap.io`
<br>
`MAIL_PORT=2525`
<br>
`MAIL_USERNAME=202c391237bfd1`
<br>
`MAIL_PASSWORD=75430228dc45e3`

3. **Run the Queue Worker:** To process the queued jobs, you need to run the queue worker. Open a terminal window and navigate to your project directory. Then, run the following command:

```bash
php artisan queue:work
```

By default, the queue worker processes jobs from the default queue. You can specify a different queue using the --queue option:

```bash
php artisan queue:work --queue=high
```

3. **Running failed jobs (OPTIONAL)**

To retry failed jobs, you can use the queue:retry command with the ID of the failed job:

```bash
php artisan queue:retry {id}
```

If you want to clear all failed jobs from the failed jobs table, you can use the **queue:flush** command:

```bash
php artisan queue:flush
```

## Basketball Arena Form System

### Complete Form Structure
The Basketball Arena form is a comprehensive booking system with 6 pages and 18 fields:

#### Page 1: Contact Information
- **Contact Person Name** (text, required)
- **Phone Number** (text, required)
- **Email Address** (email, required)

#### Page 2: Booking Details
- **Preferred Date** (date, required, future dates only)
- **Start Time** (time, required)
- **End Time** (time, required, must be after start time)
- **Duration** (number, 1-8 hours, required)

#### Page 3: Court & Activity
- **Court Type** (select: Indoor/Outdoor, Full/Half Court)
- **Court Number** (number, optional, 1-10)
- **Booking Type** (select: Practice, Game, Tournament, Training)
- **Number of Players** (number, 1-20, required)

#### Page 4: Equipment & Services
- **Equipment Needed** (checkboxes: Basketballs, Scoreboard, Referee, First Aid, Water, Towels)
- **Skill Level** (radio: Beginner, Intermediate, Advanced, Professional)

#### Page 5: Additional Information
- **Special Requirements** (textarea, optional)
- **Additional Notes** (textarea, optional)
- **Team Roster** (file attachment, PDF/DOC, optional)

#### Page 6: Agreement
- **Terms and Conditions** (checkbox, required)
- **Arena Rules Agreement** (checkbox, required)
- **Court Quality Rating** (1-5 stars, optional)

### Field Types Used
- **Text**: Contact information
- **Email**: Email validation
- **Date**: Date picker with validation
- **Time**: Time picker
- **Number**: Numeric inputs with min/max
- **Select**: Dropdown options
- **Checkbox**: Multiple selections
- **Radio**: Single choice
- **Textarea**: Long text input
- **Attachment**: File upload
- **Rating**: Star rating system

### Validation Rules
- **Required fields**: Contact info, booking details, court type, player count, agreements
- **Date validation**: Future dates only
- **Time validation**: End time must be after start time
- **Number ranges**: Duration (1-8 hours), Players (1-20), Court number (1-10)
- **File types**: PDF, DOC, DOCX for team roster
- **Email format**: Valid email addresses

## Multi-Tenant Architecture

### Access Points

#### Central Admin (Super Admin)
- **URL**: `http://main.renturo.test/super-admin`
- **Purpose**: Manage tenants, users, and system-wide settings
- **Features**: Tenant management, user administration, system configuration

#### Tenant Admin
- **URL**: `http://main.renturo.test/admin`
- **Purpose**: Manage tenant-specific content and settings
- **Features**: User management, post management, dynamic forms, categories

#### Tenant Owner
- **URL**: `http://main.renturo.test/owner`
- **Purpose**: Business owner dashboard and management
- **Features**: Post management, analytics, user management, settings

#### Mobile App (Flutter)
- **Purpose**: Client-facing mobile application
- **Features**: Store management, dynamic forms, bookings, messaging

### API Endpoints

#### Central APIs
- **Base URL**: `https://renturo.ngrok.app/api/v1/`
- **Endpoints**: Authentication, user management, tenant management

#### Client APIs (Mobile App)
- **Base URL**: `https://renturo.ngrok.app/api/client/v1/`
- **Endpoints**: Stores, forms, categories, chats, messages

#### User APIs
- **Base URL**: `https://renturo.ngrok.app/api/user/v1/`
- **Endpoints**: Form submissions, user-specific data

## Development Workflow

### 1. Start Backend Services
```bash
# Start Laravel server
php artisan serve --host=0.0.0.0 --port=8001

# Start ngrok tunnel (in separate terminal)
ngrok http --domain=renturo.ngrok.app 8001

# Start queue worker (in separate terminal)
php artisan queue:work
```

### 2. Start Frontend Services
```bash
# Start Vite dev server
npm run dev
```

### 3. Start Mobile App
```bash
cd ../client
flutter run --flavor dev -t lib/main_dev.dart
```

### Testing the Basketball Arena Form

#### Admin Panel Testing
1. **Access Admin Panel**: `http://main.renturo.test/admin`
2. **Navigate to**: Post Management → Dynamic Forms
3. **Find**: "Basketball Arena" form
4. **View Form Builder**: See all 6 pages and 18 fields
5. **Test Form Builder**: Drag and drop functionality

#### Form Submission Testing
1. **Access Form**: Via mobile app or web interface
2. **Fill All Pages**: Complete all 6 pages
3. **Test Validation**: Try submitting with missing required fields
4. **Test File Upload**: Upload team roster document
5. **Submit Form**: Verify all data is captured

#### Database Verification
```bash
# Check form data in database
php artisan tinker
>>> App\Models\DynamicForm::with('dynamicFormPages.dynamicFormFields')->find(1)
```

## Troubleshooting

### Common Issues

#### Database Issues
```bash
# Clear all caches
php artisan config:clear
php artisan route:clear
php artisan cache:clear

# Reset tenant databases
php artisan tenants:migrate-fresh --seed
```

#### Optimization
**Run the Command:** Execute the following command to run the optimization:

```bash
php artisan optimize
```

This command performs several optimization tasks, including:
- Generating a "compiled" class file for your Blade views for faster rendering.
- Caching the configuration files to reduce disk I/O.
- Caching the routes to improve route registration speed.
- Clearing the compiled class file if it exists to force recompilation.

#### Valet Issues
```bash
# Restart Valet
valet restart

# Check Valet status
valet status
```

#### Port Conflicts
```bash
# Check what's using a port
lsof -i :8001

# Kill processes using a port
pkill -f "php artisan serve"
```