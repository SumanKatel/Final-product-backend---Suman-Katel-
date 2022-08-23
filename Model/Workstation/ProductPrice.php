<?php

namespace App\Model\Workstation;

use Illuminate\Database\Eloquent\Model;

class ProductPrice extends Model {
	
    protected $table = 'tbl_product_price';
    protected $guarded = ['id'];    
}
