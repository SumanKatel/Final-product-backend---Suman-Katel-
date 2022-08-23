<?php

namespace App\Model\Workstation;

use Illuminate\Database\Eloquent\Model;

class InventoryAction extends Model {
	
    protected $table = 'tbl_inventory_action';
    protected $guarded = ['id'];    
}
