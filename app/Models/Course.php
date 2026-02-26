<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $table = 'courses';

    protected $fillable = [
        'instructor_id',
        'category_id',
        'title',
        'slug',
        'short_description',
        'full_description',
        'price',
        'currency',
        'is_published',
        'status',
        'level',
        'duration_minutes',
        'thumbnail_url',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_published' => 'boolean',
        'duration_minutes' => 'integer',
        'rating_avg' => 'decimal:2',
        'rating_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function modules()
    {
        return $this->hasMany(CourseModule::class);
    }

    public function lessons()
    {
        // hasManyThrough(Final, Through, throughForeignKey, finalForeignKey, localKey = 'id', throughLocalKey = 'id')
        return $this->hasManyThrough(
            Lesson::class,
            CourseModule::class,
            'course_id', // Foreign key on course_modules table...
            'module_id', // Foreign key on lessons table...
            'id', // Local key on courses table
            'id'  // Local key on course_modules table
        );
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function reviews()
    {
        return $this->hasMany(CourseReview::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }

    public function forumThreads()
    {
        return $this->hasMany(ForumThread::class);
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }
}
