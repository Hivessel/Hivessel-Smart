<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $fillable = ['plugin', 'user_id', 'context'];
    protected $casts = [
        'context' => 'array'
    ];
}
