<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AnalysisTemplates extends Model
{
    public $timestamps = false;
    protected $fillable = ['analysis_plate_title', 'text'];
}
