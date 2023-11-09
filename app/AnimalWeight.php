<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AnimalWeight extends Model
{
    public $timestamps = false;
    protected $fillable = ['visit_date_id', 'date_of_visit', 'anymal_id', 'weight'];
}
