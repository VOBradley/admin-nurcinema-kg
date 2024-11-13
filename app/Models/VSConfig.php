<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VSConfig extends Model
{
    protected $fillable = ['slug', 'value'];
    protected $casts = [
        'value' => 'boolean',
    ];
}
