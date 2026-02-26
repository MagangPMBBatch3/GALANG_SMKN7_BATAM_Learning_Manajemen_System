<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    // Table has custom timestamps (sent_at); disable Eloquent's automatic timestamps
    public $timestamps = false;

    protected $table = 'notifications';

    protected $fillable = [
        'user_id',
        'type',
        'payload',
        'is_read',
        'sent_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'is_read' => 'boolean',
        'sent_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
