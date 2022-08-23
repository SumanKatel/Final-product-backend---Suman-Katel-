<?php

namespace App\Model\Workstation;

use Illuminate\Database\Eloquent\Model;

class CategoryAttributeRelation extends Model {
	
    protected $table = 'tbl_product_category_attributes';
    protected $guarded = ['id'];    
}
