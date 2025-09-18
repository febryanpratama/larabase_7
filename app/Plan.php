<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Plan extends Model
{
    use SoftDeletes;
    //
    protected $guarded = ['id'];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
