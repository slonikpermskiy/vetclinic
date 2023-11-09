<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Visits extends Model
{
    public $timestamps = false;
	public $primaryKey = 'visit_id';
    protected $fillable = ['visit_date_id', 'date_of_visit', 'doctor', 'patient_id', 'visit_purpose', 'visit_type', 'complaints', 'inspection_results', 'clinic_comments', 'research_needed', 'analisys_needed', 'recomendation', 'old_id'];		
}
