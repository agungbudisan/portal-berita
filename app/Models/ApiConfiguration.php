<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiConfiguration extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'api_key', 'base_url', 'is_active', 'parameters'
    ];

    protected $casts = [
        'parameters' => 'array',
        'is_active' => 'boolean'
    ];
}
