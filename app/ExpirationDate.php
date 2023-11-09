<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExpirationDate extends Model
{
    public $timestamps = false;
    protected $fillable = ['expiration_date'];
}
