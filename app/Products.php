<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    public $timestamps = false;
    protected $fillable = ['product_title', 'product_edizm', 'product_in_price', 'product_out_price', 'category', 'old_id'];
	
	
	public function getCategorynameAttribute($value)
    {		
		$category = ProductCategory::where('id', $value)->first();

		if ($category) {
			return ($category->title);
		} else {
			return null;
		}
    }
	
}
