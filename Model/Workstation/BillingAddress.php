<?php

namespace App\Model\Workstation;

use Illuminate\Database\Eloquent\Model;

class BillingAddress extends Model {
	
    protected $table = 'tbl_user_delivery_address';
    protected $guarded = ['id'];    
}
