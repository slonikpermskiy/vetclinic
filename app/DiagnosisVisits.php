<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DiagnosisVisits extends Model
{
    public $timestamps = false;
    protected $fillable = ['visit_date_id', 'anymal_id', 'diagnosis_id', 'need_aprove_id', 'permanent_id'];
}
