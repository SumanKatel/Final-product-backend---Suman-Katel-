<?php

namespace App\Model\admin;

use Illuminate\Database\Eloquent\Model;

class ProductRating extends Model {
	
    protected $table = 'tbl_rating_product';
    protected $guarded = ['id'];
}
