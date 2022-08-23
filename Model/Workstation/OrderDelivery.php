<?php

namespace App\Model\Workstation;

use Illuminate\Database\Eloquent\Model;

class OrderDelivery extends Model {
	
    protected $table = 'tbl_order_delivery';
    protected $guarded = ['id'];    
}
