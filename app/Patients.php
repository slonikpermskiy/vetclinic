<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Patients extends Model
{
	public $timestamps = false;
	public $primaryKey = 'patient_id';
    protected $fillable = ['client_id', 'short_name', 'full_name', 'sex_id', 'animal_type_id', 'breed_id', 'color_id', 'date_of_birth', 'aprox_date', 'rip', 'tatoo', 'chip', 'registration_date', 'castrated', 'additional_info', 'old_id'];
}
