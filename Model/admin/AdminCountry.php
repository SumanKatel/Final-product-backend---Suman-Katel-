<?php

namespace App\Model\admin;

use Illuminate\Database\Eloquent\Model;

Class AdminCountry extends Model{
	protected $table = 'tbl_country';
	protected $guarded = ['id'];

	public function remittance(){
		$data = $this->hasMany(AdminRemittance::class, 'sender_country_id', 'id');
		return $data;
	}

	public function receiverRemittance(){
		$data = $this->hasMany(AdminRemittance::class, 'reciever_country_id', 'id');
		return $data;
	}
}


?>