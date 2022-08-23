<?php

namespace App\Model\admin;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model {
    
    protected $table = 'coupons';
    protected $guarded = ['id'];
}
