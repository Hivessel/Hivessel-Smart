<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    protected $fillable = ['grade_id', 'subject_id', 'quarter_id', 'content', 'active'];

    public function grade(){
        return $this->belongsTo(Grade::class);
    }

    public function subject(){
        return $this->belongsTo(Subject::class);
    }

    public function quarter(){
        return $this->belongsTo(Quarter::class);
    }

    public function competencies(){
        return $this->hasMany(Competency::class);
    }
}
