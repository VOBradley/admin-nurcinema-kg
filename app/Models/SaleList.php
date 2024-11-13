<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleList extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_id',
        'seats',
        'seats_text',
        'show_id',
        'show_info',
        'sum',
        'price',
        'sale',
        'reservation_id',
        'currency',
        'status',
        'email',
        'phone',
        'payment_type',
        'ticketon_callback_info',
        'ticket'
    ];

    // Определите типы данных для атрибутов JSON, если необходимо
    protected $casts = [
        'seats' => 'array',
        'seats_text' => 'array',
        'show_info' => 'array',
        'ticketon_callback_info' => 'array',
        'ticket' => 'array',
    ];
}
