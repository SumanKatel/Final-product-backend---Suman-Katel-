<?php

namespace App\Model\Workstation;

use Illuminate\Database\Eloquent\Model;

class ProductOrderBilling extends Model {
	
    protected $table = 'tbl_product_order_billing';
    protected $guarded = ['id'];    
}
