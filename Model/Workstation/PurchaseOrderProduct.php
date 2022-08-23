<?php

namespace App\Model\Workstation;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderProduct extends Model {
	
    protected $table = 'tbl_purchase_order_product';
    protected $guarded = ['id'];    
}
