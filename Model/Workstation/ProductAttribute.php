<?php

namespace App\Model\Workstation;

use Illuminate\Database\Eloquent\Model;

class ProductAttribute extends Model {
	
    protected $table = 'tbl_product_attributes';
    protected $guarded = ['id'];    
}
