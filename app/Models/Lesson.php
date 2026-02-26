<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;

    protected $table = 'lessons';

    protected $fillable = [
        'module_id',
        'course_id',
        'title',
        'slug',
        'content_type',
        'content',
        'media_url',
        'duration_seconds',
        'is_downloadable',
        'position',
    ];

    protected $casts = [
        'duration_seconds' => 'integer',
        'is_downloadable' => 'boolean',
        'position' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function module()
    {
        return $this->belongsTo(CourseModule::class, 'module_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function resources()
    {
        return $this->hasMany(LessonResource::class);
    }

    public function progress()
    {
        return $this->hasMany(LessonProgress::class);
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }

    public function forumThreads()
    {
        return $this->hasMany(ForumThread::class);
    }
}
