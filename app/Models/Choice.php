<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Choice extends Model
{
    use HasFactory;

    protected $table = 'choices';

    public $timestamps = false; // Disable timestamps

    protected $fillable = [
        'question_id',
        'text',
        'is_correct',
        'position',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'position' => 'integer',
    ];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
