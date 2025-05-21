<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class News extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'image_url',
        'cloudinary_public_id',
        'source',
        'source_url',
        'status',
        'views_count',
        'api_id',
        'category_id',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function approvedComments()
    {
        return $this->hasMany(Comment::class)->where('status', 'approved');
    }

    public function bookmarks()
    {
        return $this->hasMany(Bookmark::class);
    }

    public function bookmarkedByUsers()
    {
        return $this->belongsToMany(User::class, 'bookmarks', 'news_id', 'user_id')
            ->withTimestamps();
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function isBookmarkedByUser(User $user)
    {
        return $this->bookmarks()->where('user_id', $user->id)->exists();
    }

    public function getImageUrlAttribute()
    {
        // Hindari error offset null
        if (array_key_exists('image_url', $this->attributes) && $this->attributes['image_url']) {
            return $this->attributes['image_url'];
        }
        return asset('images/placeholder.jpg');
    }

    public function getPurifiedContentAttribute()
    {
        if (empty($this->content)) {
            return '';
        }

        $purifier = app('htmlpurifier');
        return $purifier->purify($this->content);
    }
}
