<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Breeds extends Model
{	
	public $timestamps = false;
	public $primaryKey = 'breed_id';
    protected $fillable = ['breed_title', 'animal_type_id', 'old_id'];
}
