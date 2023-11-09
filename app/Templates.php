<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Templates extends Model
{
    public $timestamps = false;
    protected $fillable = ['plate_title', 'type', 'text'];
}
