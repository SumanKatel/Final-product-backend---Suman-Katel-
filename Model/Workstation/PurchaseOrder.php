<?php

namespace App\Model\Workstation;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model {
	
    protected $table = 'tbl_purchase_order';
    protected $guarded = ['id'];    
}
