<?php

namespace App\Model\admin;

use Illuminate\Database\Eloquent\Model;

class AdminRemittance extends Model
{
    protected $table = 'tbl_remittance';
    protected $guarded = ['id'];

    public function customer(){
        $data = $this->belongsTo(Customer::class, 'customer_id', 'id');
        return $data;
    }

    public function moneyTransfer(){
        $data = $this->belongsTo(AdminMoneyTransfer::class, 'money_transfer_id', 'id');
        return $data;
    }

    public function agent(){
        $data = $this->belongsTo(AdminAgent::class, 'agent_id', 'id');
        return $data;
    }

    //sender
    public function senderCountry(){
        $data = $this->belongsTo(AdminCountry::class, 'sender_country_id', 'id');
        return $data;
    }

    public function senderState(){
        $data = $this->belongsTo(AdminState::class, 'sender_state_id', 'id');
        return $data;
    }

    public function senderDistrict(){
        $data = $this->belongsTo(AdminDistrict::class, 'sender_district_id', 'id');
        return $data;
    }

    //receiver
    public function receiverCountry(){
        $data = $this->belongsTo(AdminCountry::class, 'receiver_country_id', 'id');
        return $data;
    }

    public function receiverState(){
        $data = $this->belongsTo(AdminState::class, 'receiver_state_id', 'id');
        return $data;
    }

    public function receiverDistrict(){
        $data = $this->belongsTo(AdminDistrict::class, 'receiver_district_id', 'id');
        return $data;
    }
}
