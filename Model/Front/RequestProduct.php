<?php

namespace App\Model\Front;

use Illuminate\Database\Eloquent\Model;

class RequestProduct extends Model {
	
    protected $table = 'tbl_request_product';
    protected $guarded = ['id'];    
}
