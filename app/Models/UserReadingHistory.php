<?php

// app/Models/UserReadingHistory.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserReadingHistory extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'saved_article_id', 'read_at'];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function savedArticle()
    {
        return $this->belongsTo(SavedArticle::class);
    }
}
