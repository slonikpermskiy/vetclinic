<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bills extends Model
{
    public $timestamps = false;
	public $primaryKey = 'bill_id';
    protected $fillable = ['date_of_bill', 'staff', 'staff_id', 'patient_id', 'product_text', 'product_discount', 'product_summ', 'service_text', 'service_discount', 'service_summ', 'bill_summ', 'paied'];
}
