<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    public function user(){
        return $this->belongsTo("\App\User");
    }
}
