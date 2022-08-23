<?php

namespace App\Model\Workstation;

use Illuminate\Database\Eloquent\Model;

class WarehouseProduct extends Model {
	
    protected $table = 'tbl_warehouse_product';
    protected $guarded = ['id'];    
}
