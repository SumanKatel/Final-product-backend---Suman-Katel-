<?php

namespace App\Model\Front;

use Illuminate\Database\Eloquent\Model;

class ProductWishList extends Model {
	
    protected $table = 'tbl_product_wishlist';
    protected $guarded = ['id'];    
}
