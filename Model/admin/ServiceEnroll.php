<?php

namespace App\Model\admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ServiceEnroll extends Model {

    protected $table = 'tbl_service_enroll';
    protected $guarded = ['id'];

    public function customer(){
    	$data = $this->belongsTo(Customer::class, 'customer_id', 'id');
    	return $data;
    }

    public function otherService(){
    	$data = $this->belongsTo(AdminOtherService::class, 'service_id', 'id');
    	return $data;
    }
}
