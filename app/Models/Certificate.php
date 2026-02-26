<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;

    protected $table = 'certificates';
    public $timestamps = false;

    protected $fillable = [
        'enrollment_id',
        'user_id',
        'course_id',
        'issued_at',
        'cert_number',
        'pdf_url',
        'digital_signature',
        'data',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'data' => 'array',
    ];

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
