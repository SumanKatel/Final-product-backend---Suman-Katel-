<?php

namespace App\Model\admin;

use Illuminate\Database\Eloquent\Model;

class AdminSeat extends Model
{
    protected $table = 'tbl_vehicle_seat';
    protected $guarded = ['id'];

    public function ticketBooking(){
        $data = $this->hasMany(AdminTicketBooking::class, 'seat_id', 'id');
        return $data;
    }
}
