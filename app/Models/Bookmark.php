<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bookmark extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'saved_article_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function savedArticle()
    {
        return $this->belongsTo(SavedArticle::class);
    }
}
