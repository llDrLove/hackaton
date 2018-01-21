<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Response extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'respondant_id', 'has_responded'
    ];
}
