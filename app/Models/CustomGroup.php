<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomGroup extends Model
{
    protected $fillable = ['slug', 'title', 'sort_order', 'is_active'];
    public function fields()
    {
        return $this->hasMany(CustomField::class);
    }

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
