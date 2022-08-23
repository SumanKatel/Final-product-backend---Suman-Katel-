<?php

namespace App\Model\Workstation;

use Illuminate\Database\Eloquent\Model;

class ProductInventory extends Model {
	
    protected $table = 'tbl_product_inventory';
    protected $guarded = ['id'];    
}
