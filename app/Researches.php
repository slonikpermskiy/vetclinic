<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Researches extends Model
{
    public $timestamps = false;
	public $primaryKey = 'research_id';
    protected $fillable = ['date_of_research', 'doctor', 'patient_id', 'visit_id', 'research_name', 'research_text'];		
}
