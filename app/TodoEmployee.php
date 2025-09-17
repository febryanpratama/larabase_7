<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TodoEmployee extends Model
{
    //

    protected $guarded = ['id'];

    public function assigns(){
        return $this->hasMany(TodoAssign::class, 'todo_id', 'id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
