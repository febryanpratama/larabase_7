<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Division extends Model
{
    //
    use SoftDeletes;

    protected $guarded = ['id'];

    public function employees()
    {
        return $this->hasMany(Employee::class, 'division_id');
    }
}
