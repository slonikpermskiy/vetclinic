<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DiagnosisTypes extends Model
{
    public $timestamps = false;
    protected $fillable = ['diagnosis_title', 'old_id'];
}
