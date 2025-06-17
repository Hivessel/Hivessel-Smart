<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $fillable = ['plugin', 'user_id', 'context', 'options'];
    protected $casts = [
        'context' => 'array',
        'options' => 'array',
    ];
}
