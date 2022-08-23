<?php

namespace App\Model\Front;

use Illuminate\Database\Eloquent\Model;

class ProductQuestion extends Model {
	
    protected $table = 'tbl_product_questions';
    protected $guarded = ['id'];    
}
