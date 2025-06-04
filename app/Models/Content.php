<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    protected $fillable = ['grade_id', 'subject_id', 'quarter_id', 'content', 'active'];

    public function competencies(){
        return $this->hasMany(Competency::class);
    }
}
