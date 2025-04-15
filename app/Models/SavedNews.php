<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavedNews extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'title', 'description', 'source', 'source_url',
        'image_url', 'api_id', 'content', 'published_at'
    ];

    protected $casts = [
        'content' => 'array',
        'published_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
