<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForumThread extends Model
{
    use HasFactory;

    protected $table = 'forum_threads';

    protected $fillable = [
        'course_id',
        'lesson_id',
        'user_id',
        'title',
        'body',
        'is_locked',
        'is_sticky',
    ];

    protected $casts = [
        'is_locked' => 'boolean',
        'is_sticky' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function posts()
    {
        return $this->hasMany(ForumPost::class, 'thread_id');
    }

    public function likes()
    {
        return $this->hasMany(ThreadLike::class, 'thread_id', 'id');
    }
}
