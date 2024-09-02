<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomField extends Model
{
    protected $fillable = ['slug', 'title', 'is_active', 'sort_order', 'href'];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
