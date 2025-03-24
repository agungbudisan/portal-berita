<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavedArticle extends Model
{
    use HasFactory;

    protected $fillable = [
        'article_id', 'title', 'description', 'url', 'url_to_image',
        'source_name', 'published_at', 'content', 'category_id',
        'user_id', 'is_published', 'view_count'
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_published' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bookmarks()
    {
        return $this->hasMany(Bookmark::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function readingHistories()
    {
        return $this->hasMany(UserReadingHistory::class);
    }

    public function views()
    {
        return $this->hasMany(ArticleView::class);
    }

    // Scope untuk artikel yang dipublikasikan
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    // Scope untuk artikel populer
    public function scopePopular($query, $limit = 5)
    {
        return $query->published()
            ->orderBy('view_count', 'desc')
            ->limit($limit);
    }

    // Scope untuk artikel terbaru
    public function scopeLatest($query, $limit = null)
    {
        $query = $query->published()->orderBy('published_at', 'desc');

        if ($limit) {
            $query->limit($limit);
        }

        return $query;
    }

    // Increment view count
    public function incrementViewCount()
    {
        $this->increment('view_count');
    }
}
