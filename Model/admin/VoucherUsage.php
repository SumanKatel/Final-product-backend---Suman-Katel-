<?php

namespace App\Model\admin;

use Illuminate\Database\Eloquent\Model;

class VoucherUsage extends Model {
    
    protected $table = 'voucher_usages';
    protected $guarded = ['id'];
}
