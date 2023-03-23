<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use SoftDeletes;

    public function answers()
    {
        return $this->hasMany("\App\Models\Answer");
    }

    public function correctAnswer()
    {
        return $this->hasOne("\App\Models\CorrectAnswer");
    }
}
