<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseModule;
use App\Models\Lesson;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\Badge;
use App\Models\Notification;
use Illuminate\Support\Str;

class InitialDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Roles
        $adminRole = Role::firstOrCreate(['name' => 'admin'], ['display_name' => 'Administrator', 'description' => 'Platform administrator']);
        $studentRole = Role::firstOrCreate(['name' => 'student'], ['display_name' => 'Student', 'description' => 'Student user']);
        $instructorRole = Role::firstOrCreate(['name' => 'instructor'], ['display_name' => 'Instructor', 'description' => 'Course instructor']);

        // Users
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Admin User', 'password' => 'password', 'remember_token' => Str::random(10)]
        );
        if (!$admin->hasRole('admin')) {
            $admin->roles()->attach($adminRole->id, ['assigned_by' => $admin->id, 'assigned_at' => now()]);
        }

        // Create several students
        $students = User::factory()->count(10)->create();
        foreach ($students as $s) {
            if (!$s->hasRole('student')) {
                $s->roles()->attach($studentRole->id, ['assigned_by' => $admin->id, 'assigned_at' => now()]);
            }
        }

        // Categories
        $categories = Category::factory()->count(4)->create();

        // Courses
        $courses = Course::factory()->count(6)->create()->each(function ($course) use ($students) {
            // Create modules and lessons
            $modules = CourseModule::factory()->count(rand(2,4))->make()->each(function ($m) use ($course) {
                $m->course_id = $course->id;
                $m->save();
                Lesson::factory()->count(rand(2,5))->make()->each(function ($l) use ($course, $m) {
                    $l->course_id = $course->id;
                    $l->module_id = $m->id;
                    $l->position = 1;
                    $l->save();
                });
            });
        });

        // Enroll some students (randomly) and create payments for paid courses
        foreach ($students->take(6) as $idx => $student) {
            $course = $courses->random();
            $enrollment = Enrollment::create([
                'user_id' => $student->id,
                'course_id' => $course->id,
                'status' => 'active',
                'progress_percent' => rand(0, 80),
                'price_paid' => $course->price > 0 ? $course->price : 0,
                'currency' => 'IDR',
            ]);

            if ($course->price > 0) {
                Payment::create([
                    'enrollment_id' => $enrollment->id,
                    'user_id' => $student->id,
                    'course_id' => $course->id,
                    'amount' => $course->price,
                    'currency' => 'IDR',
                    'method' => 'fake_card',
                    'status' => 'paid',
                    'transaction_ref' => 'tx_demo_' . rand(1000,9999),
                    'payment_data' => ['card_last4' => '4242'],
                    'paid_at' => now(),
                ]);
            }
        }

        // Badges
        Badge::firstOrCreate(['code' => 'WELCOME'], ['name' => 'Welcome', 'description' => 'Given to new users']);
        Badge::firstOrCreate(['code' => 'COMPLETER'], ['name' => 'Completer', 'description' => 'Complete a course']);

        // Notifications (use payload to store message)
        Notification::create(['user_id' => $admin->id, 'type' => 'system', 'payload' => ['message' => 'Seeded admin notification'], 'sent_at' => now()]);

        // Quick log
        $this->command->info('Initial sample data seeded (roles, users, categories, courses, modules, lessons, enrollments, payments, badges, notifications).');
    }
}
