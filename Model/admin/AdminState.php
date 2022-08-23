<?php

namespace App\Model\admin;

use Illuminate\Database\Eloquent\Model;

Class AdminState extends Model{
	protected $table = 'tbl_state';
	protected $guarded = ['id'];

	public function remittance(){
		$data = $this->hasMany(AdminRemittance::class,'sender_state_id', 'id');
		return $data;
	}

	public function receiverRemittance(){
		$data = $this->hasMany(AdminRemittance::class,'sender_state_id', 'id');
		return $data;
	}
}


?>