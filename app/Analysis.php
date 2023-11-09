<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Analysis extends Model
{
    public $timestamps = false;
	public $primaryKey = 'analysis_id';
    protected $fillable = ['date_of_analysis', 'doctor', 'patient_id', 'visit_id', 'analysis_name', 'analysis_text'];
}
