<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Animal_types extends Model
{
    public $timestamps = false;
	public $primaryKey = 'animal_type_id';
    protected $fillable = ['type_title', 'old_id'];
}
