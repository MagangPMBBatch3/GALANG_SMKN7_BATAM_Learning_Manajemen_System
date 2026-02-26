<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    protected $table = 'quizzes';

    protected $fillable = [
        'course_id',
        'lesson_id',
        'title',
        'description',
        'passing_score',
        'time_limit_seconds',
        'attempts_allowed',
    ];

    protected $casts = [
        'passing_score' => 'integer',
        'time_limit_seconds' => 'integer',
        'attempts_allowed' => 'integer',
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

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function submissions()
    {
        return $this->hasMany(QuizSubmission::class);
    }
}
