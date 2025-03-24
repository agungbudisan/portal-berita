<?php

// app/Models/Comment.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'saved_article_id', 'content',
        'is_approved', 'parent_id'
    ];

    protected $casts = [
        'is_approved' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function savedArticle()
    {
        return $this->belongsTo(SavedArticle::class);
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    // Scope untuk komentar yang disetujui
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    // Scope untuk komentar induk (bukan reply)
    public function scopeParents($query)
    {
        return $query->whereNull('parent_id');
    }
}
