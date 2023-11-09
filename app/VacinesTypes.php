<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VacinesTypes extends Model
{
    public $timestamps = false;
    protected $fillable = ['vacine_title', 'old_id'];
}
