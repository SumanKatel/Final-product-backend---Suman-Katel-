<?php

namespace App\Model\admin;

use Illuminate\Database\Eloquent\Model;

class AdminOtherService extends Model
{
    protected $table = 'tbl_other_service';
    protected $guarded = ['id']; 

    public function customer(){
    	$data = $this->belongsToMany(Customer::class, 'tbl_service_enroll', 'service_id', 'customer_id');
    	return $data;
    }

    public function serviceEnroll(){
    	$data = $this->hasMany(ServiceEnroll::class, 'service_id', 'id');
    	return $data;
    }
}
