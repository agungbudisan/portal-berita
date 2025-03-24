<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'api_key', 'base_url', 'default_params',
        'is_active', 'cache_time_minutes'
    ];

    protected $casts = [
        'default_params' => 'array',
        'is_active' => 'boolean',
    ];

    // Scope untuk config aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
