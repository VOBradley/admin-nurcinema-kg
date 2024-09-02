<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomImage extends Model
{
    protected $fillable = ['slug', 'image'];
}
