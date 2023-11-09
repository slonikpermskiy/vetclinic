<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vacines extends Model
{
    public $timestamps = false;
	public $primaryKey = 'vacines_id';
    protected $fillable = ['date_of_vacine', 'doctor', 'patient_id', 'vacine_name'];
}
