<?php

namespace App\Model\admin;

use Illuminate\Database\Eloquent\Model;

class AdminMoneyTransfer extends Model
{
    protected $table = 'tbl_money_transfer';
    protected $guarded = ['id'];

    public function remittance(){
        $data = $this->hasMany(AdminRemittance::class, 'money_transfer_id', 'id');
        return $data;
    }

    
}
