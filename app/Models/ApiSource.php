<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiSource extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'url',
        'api_key',
        'status',
        'last_sync',
        'news_count',
    ];

    protected $casts = [
        'last_sync' => 'datetime',
    ];

    public function isActive()
    {
        return $this->status === 'active';
    }

}
