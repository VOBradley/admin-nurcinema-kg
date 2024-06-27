<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketonCache extends Model
{
    protected $fillable = ['cache_key', 'data'];
    protected $casts = ['data' => 'array',];
}
