<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
	public $timestamps = false;
	public $primaryKey = 'staff_id';
    protected $fillable = ['last_name', 'first_name', 'middle_name', 'position'];
}
