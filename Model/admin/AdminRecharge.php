<?php

namespace App\Model\admin;

use Illuminate\Database\Eloquent\Model;

class AdminRecharge extends Model
{
    protected $table = 'tbl_recharge';
    protected $guarded = ['id'];

    public function telecommunication(){
    	$data = $this->belongsTo(AdminTelecommunication::class, 'telecommunication_id', 'id');
    	return $data;
    }

    public function customer(){
    	$data = $this->belongsTo(Customer::class, 'customer_id', 'id');
    	return $data;
    }
}
