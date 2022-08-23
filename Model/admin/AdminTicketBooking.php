<?php

namespace App\Model\admin;

use Illuminate\Database\Eloquent\Model;

class AdminTicketBooking extends Model
{
    protected $table = 'tbl_ticket_booking';
    protected $guarded = ['id'];

    public function vehicle(){
    	$data = $this->belongsTo(AdminVehicle::class, 'vehicle_id', 'id');
    	return $data;
    }

    public function seat(){
    	$data = $this->belongsTo(AdminSeat::class, 'seat_id', 'id');
    	return $data;
    }

    public function customer(){
    	$data = $this->belongsTo(Customer::class, 'customer_id', 'id');
    	return $data;
    }

}
