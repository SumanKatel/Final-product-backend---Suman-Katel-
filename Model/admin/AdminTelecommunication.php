<?php

namespace App\Model\admin;

use Illuminate\Database\Eloquent\Model;

class AdminTelecommunication extends Model
{
    protected $table = 'tbl_telecommunication';
    protected $guarded = ['id'];

    public function recharge(){
    	$data = $this->hasMany(AdminRecharge::class, 'telecommunication_id', 'id');
    	return $data;
    }
}
