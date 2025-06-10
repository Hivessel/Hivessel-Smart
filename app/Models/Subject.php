<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = ['grade_id', 'subject', 'active'];

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }
}
