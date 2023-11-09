<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Colors extends Model
{
    public $timestamps = false;
	public $primaryKey = 'color_id';
    protected $fillable = ['color_title', 'old_id'];
}
