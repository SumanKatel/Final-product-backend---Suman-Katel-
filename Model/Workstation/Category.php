<?php

namespace App\Model\Workstation;

use Illuminate\Database\Eloquent\Model;

class Category extends Model {
	
    protected $table = 'tbl_product_category';
    protected $guarded = ['id'];    
}
