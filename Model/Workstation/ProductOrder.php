<?php

namespace App\Model\Workstation;

use Illuminate\Database\Eloquent\Model;

class ProductOrder extends Model {
	
    protected $table = 'tbl_product_order';
    protected $guarded = ['id'];    
}
