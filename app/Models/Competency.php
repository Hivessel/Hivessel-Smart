<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Competency extends Model
{
    protected $fillable = ['content_id', 'competency', 'attachment', 'active'];

    public function content(){
        return $this->belongsTo(Content::class);
    }
}
