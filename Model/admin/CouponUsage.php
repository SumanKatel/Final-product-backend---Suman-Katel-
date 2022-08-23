<?php

namespace App\Model\admin;

use Illuminate\Database\Eloquent\Model;

class CouponUsage extends Model {
    
    protected $table = 'coupon_usages';
    protected $guarded = ['id'];
}
