<?php

namespace App\Model\Front;

use Illuminate\Database\Eloquent\Model;

class ProductCart extends Model {
	
    protected $table = 'tbl_product_cart';
    protected $guarded = ['id'];    

    public function product(){
    	return $this->belongsTo('App\Model\Workstation\Product', 'product_id', 'id')->select('id', 'product_name', 'slug');
    }

    public function user(){
    	return $this->belongsTo('App\Model\Workstation\Customer', 'user_id', 'id')->select('id', 'name', 'email', 'address');
    }

    public function billingaddress(){
    	return $this->belongsTo('App\Model\Workstation\BillingAddress', 'delivery_address_id', 'id')->select('id', 'address', 'title');
    }
}
