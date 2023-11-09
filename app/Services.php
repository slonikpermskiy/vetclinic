<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Services extends Model
{
    public $timestamps = false;
    protected $fillable = ['service_title', 'service_price', 'category', 'old_id'];
	
	public function getCategorynameAttribute($value)
    {		
		$category = ServiceCategory::where('id', $value)->first();

		if ($category) {
			return ($category->title);
		} else {
			return null;
		}
    }
}
