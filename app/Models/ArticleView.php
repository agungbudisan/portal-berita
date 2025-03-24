<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleView extends Model
{
    use HasFactory;

    protected $fillable = [
        'saved_article_id', 'ip_address', 'user_agent',
        'user_id', 'viewed_at'
    ];

    protected $casts = [
        'viewed_at' => 'datetime',
    ];

    public function savedArticle()
    {
        return $this->belongsTo(SavedArticle::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
