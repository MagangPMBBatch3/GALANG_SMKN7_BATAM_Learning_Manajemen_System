<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForumPost extends Model
{
    use HasFactory;

    protected $table = 'forum_posts';

    protected $fillable = [
        'thread_id',
        'user_id',
        'parent_post_id',
        'body',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function thread()
    {
        return $this->belongsTo(ForumThread::class, 'thread_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parentPost()
    {
        return $this->belongsTo(ForumPost::class, 'parent_post_id');
    }

    public function replies()
    {
        return $this->hasMany(ForumPost::class, 'parent_post_id');
    }

    public function likes()
    {
        return $this->hasMany(PostLike::class, 'post_id', 'id');
    }
}
