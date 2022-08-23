<?php

namespace App\Model\admin;

use Illuminate\Database\Eloquent\Model;

Class AdminDistrict extends Model{
	protected $table = 'tbl_district';
	protected $guarded = ['id'];

	public function remittance(){
		$data = $this->hasMany(AdminRemittance::class, 'sender_district_id', 'id');
		return $data;
	}

	public function receiverRemittance(){
		$data = $this->hasMany(AdminRemittance::class, 'sender_district_id', 'id');
		return $data;
	}
}


?>