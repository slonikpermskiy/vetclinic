<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pays extends Model
{
    public $timestamps = false;
	public $primaryKey = 'pay_id';
    protected $fillable = ['bill_id', 'patient_id', 'date_of_pay', 'pay_summ'];
}
