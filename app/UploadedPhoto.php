<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UploadedPhoto extends Model
{
    public $timestamps = false;
    protected $fillable = ['visit_date_id', 'anymal_id', 'image_name', 'image_old_name', 'description'];
}
