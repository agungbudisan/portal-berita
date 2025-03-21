<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description', 'is_active', 'display_order'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function savedArticles()
    {
        return $this->hasMany(SavedArticle::class);
    }

    // Scope untuk kategori aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope untuk mengurutkan kategori berdasarkan display_order
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order');
    }
}
