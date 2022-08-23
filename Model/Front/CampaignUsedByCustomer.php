<?php

namespace App\Model\Front;

use Illuminate\Database\Eloquent\Model;

class CampaignUsedByCustomer extends Model {
	
    protected $table = 'tbl_coupon_code_used';
    protected $guarded = ['id'];    
}
