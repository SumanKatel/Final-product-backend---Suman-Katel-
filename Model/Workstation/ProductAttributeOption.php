<?php

namespace App\Model\Workstation;

use Illuminate\Database\Eloquent\Model;

class ProductAttributeOption extends Model {
	
    protected $table = 'tbl_product_attributes_options';
    protected $guarded = ['id'];    
}
