<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'content', 'news_source_url', 'news_api_id', 'is_approved'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
