<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Clients extends Model
{
    public $timestamps = false;
	public $primaryKey = 'client_id';
    protected $fillable = ['last_name', 'first_name', 'middle_name', 'address', 'phone', 'phonemore', 'email', 'data_ready', 'comments'];
}
