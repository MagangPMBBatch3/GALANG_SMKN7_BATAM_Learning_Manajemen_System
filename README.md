# GALANG SMKN7 BATAM Learning Management System

A comprehensive Learning Management System (LMS) built with Laravel and GraphQL for SMKN7 BATAM.

## Table of Contents

- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Running the Application](#running-the-application)
- [Database Setup](#database-setup)
- [Features](#features)
- [Project Structure](#project-structure)
- [API Documentation](#api-documentation)
- [Testing](#testing)
- [Troubleshooting](#troubleshooting)

## Requirements

- PHP 8.2 or higher
- Composer
- Node.js (v16 or higher)
- npm or yarn
- MySQL 8.0 or higher
- Git

## Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/MagangPMBBatch3/GALANG_SMKN7_BATAM_Learning_Manajemen_System.git
   cd GALANG_SMKN7_BATAM_Learning_Manajemen_System
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Create environment file**
   ```bash
   cp .env.example .env
   ```

5. **Generate application key**
   ```bash
   php artisan key:generate
   ```

6. **Install Laravel Sanctum (API authentication)**
   ```bash
   php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
   ```

## Configuration

### Environment Setup

Edit the `.env` file with your configuration:

```env
APP_NAME="GALANG LMS"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=maxcourse
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@example.com
```

### Database Configuration

Update `config/database.php` with your MySQL credentials:

```php
'mysql' => [
    'driver' => 'mysql',
    'host' => env('DB_HOST', '127.0.0.1'),
    'port' => env('DB_PORT', '3306'),
    'database' => env('DB_DATABASE', 'forge'),
    'username' => env('DB_USERNAME', 'forge'),
    'password' => env('DB_PASSWORD', ''),
    'unix_socket' => env('DB_SOCKET', ''),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
    'strict' => true,
    'engine' => null,
],
```

## Running the Application

### Database Migration and Seeding

1. **Run migrations**
   ```bash
   php artisan migrate
   ```

2. **Seed the database** (optional)
   ```bash
   php artisan db:seed
   ```

3. **Create an admin user** (optional)
   ```bash
   php artisan tinker
   >>> User::create(['name' => 'Admin', 'email' => 'admin@example.com', 'password' => bcrypt('password')])
   ```

### Start Development Server

1. **Start Laravel development server**
   ```bash
   php artisan serve
   ```
   The application will be available at `http://localhost:8000`

2. **In another terminal, start Vite development server**
   ```bash
   npm run dev
   ```

3. **For production build**
   ```bash
   npm run build
   ```

### GraphQL Endpoint

- **Endpoint**: `http://localhost:8000/graphql`
- **Documentation**: `http://localhost:8000/graphiql` (if enabled)

## Database Setup

### Import Sample Data

If a SQL dump file exists (`progresproject.sql`), import it:

```bash
mysql -u root -p maxcourse < progresproject.sql
```

### Create Tables

```bash
php artisan migrate
```

### Key Tables

- `users` - User accounts
- `courses` - Course information
- `enrollments` - Student course enrollments
- `lessons` - Lesson content
- `quizzes` - Quiz questions and submissions
- `badges` - Gamification badges
- `forum_threads` - Discussion forum threads
- `forum_posts` - Forum post replies
- `payments` - Payment transactions
- `notifications` - User notifications
- `audit_logs` - Activity audit logs

## Features

### Learning Management
- **Courses**: Create, manage, and publish courses
- **Lessons**: Organize content into lesson modules
- **Resources**: Attach learning materials to lessons
- **Progress Tracking**: Monitor student progress through courses

### Assessment
- **Quizzes**: Create and manage quizzes with various question types
- **Submissions**: Student quiz submissions with automatic grading
- **Certificates**: Generate completion certificates

### Gamification
- **Points System**: Award points for course completion and activities
- **Badges**: Define and award achievement badges
- **Leaderboards**: Rank students by points

### Community
- **Forum Threads**: Create discussion threads
- **Forum Posts**: Reply and engage in discussions
- **Thread Locking**: Instructor control over discussions

### User Management
- **Role-Based Access**: Admin, Instructor, Student roles
- **Profile Management**: User profile and settings
- **Enrollment Management**: Manage student registrations

### Notifications
- **Real-time Notifications**: Notify users of important events
- **Email Notifications**: Send email alerts
- **Notification Management**: Mark as read, delete

### Payment Integration
- **Payment Processing**: Track course payments
- **Transaction History**: View payment records

## Project Structure

```
app/
├── Console/          # Artisan commands
├── Events/           # Event classes
├── Exceptions/       # Exception classes
├── GraphQL/          # GraphQL resolvers and mutations
│   ├── Mutations/
│   ├── Queries/
│   └── Scalars/
├── Http/
│   ├── Controllers/
│   ├── Middleware/
│   └── Kernel.php
└── Models/           # Eloquent models
    ├── User.php
    ├── Course.php
    ├── Enrollment.php
    └── ...

config/
├── app.php
├── database.php
├── lighthouse.php
└── ...

database/
├── migrations/       # Database migrations
└── seeders/         # Database seeders

graphql/
├── schema.graphql   # GraphQL schema definition
└── [Feature]/       # Feature-specific GraphQL files

resources/
├── css/
├── js/
└── views/

routes/
├── api.php          # API routes
├── web.php          # Web routes
└── ...

storage/
├── app/
├── framework/
└── logs/

tests/               # PHPUnit tests
├── Feature/
└── Unit/
```

## API Documentation

### GraphQL Schema

The GraphQL schema is defined in `graphql/schema.graphql`.

### Common Queries

```graphql
# Get all courses
query {
  courses {
    id
    title
    description
    instructor {
      id
      name
    }
  }
}

# Get user enrollments
query {
  enrollments {
    id
    course {
      title
    }
    progress
    status
  }
}
```

### Common Mutations

```graphql
# Create a new course
mutation {
  createCourse(input: {
    title: "New Course"
    description: "Course description"
  }) {
    id
    title
  }
}

# Enroll in a course
mutation {
  enrollCourse(input: {
    courseId: 1
  }) {
    id
    status
  }
}
```

## Testing

Run the test suite:

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/CourseTest.php

# Run with coverage
php artisan test --coverage
```

## Troubleshooting

### Common Issues

**Error: "No application encryption key has been specified"**
```bash
php artisan key:generate
```

**Error: "SQLSTATE[HY000]: General error: 1030 Got error from storage engine"**
- Check database connection in `.env`
- Verify MySQL is running
- Ensure database exists

**Error: "Class 'App\Models\User' not found"**
```bash
composer dump-autoload
```

**Port 8000 already in use**
```bash
php artisan serve --port=8001
```

**Node modules issues**
```bash
rm -rf node_modules package-lock.json
npm install
```

### Useful Commands

```bash
# Clear application cache
php artisan cache:clear

# Clear config cache
php artisan config:clear

# Clear view cache
php artisan view:clear

# Reload the autoloader
composer dump-autoload

# Tinker - Laravel REPL
php artisan tinker

# Check database connection
php artisan db

# View logs
tail -f storage/logs/laravel.log
```

## Support

For issues and feature requests, please visit the [GitHub Issues](https://github.com/MagangPMBBatch3/GALANG_SMKN7_BATAM_Learning_Manajemen_System/issues) page.

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

## Authors

- Magang PMB Batch 3
- SMKN7 BATAM Learning System Team

---

**Last Updated**: February 2026
**Laravel Version**: 11.x
**PHP Version**: 8.2+
