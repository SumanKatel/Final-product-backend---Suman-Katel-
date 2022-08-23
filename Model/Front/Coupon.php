<?php

namespace App\Model\Front;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model {
	
    protected $table = 'tbl_coupon_code';
    protected $guarded = ['id'];    
}
