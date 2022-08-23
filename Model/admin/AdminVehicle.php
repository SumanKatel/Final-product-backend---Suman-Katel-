<?php

namespace App\Model\admin;

use Illuminate\Database\Eloquent\Model;

class AdminVehicle extends Model
{
    protected $table = 'tbl_vehicle';
    protected $guarded = ['id'];

    public function ticketBooking(){
        $data = $this->hasMany(AdminTicketBooking::class, 'vehicle_id', 'id');
        return $data;
    }
    
    public function vehicleRoute(){
        $data = $this->hasMany(AdminVehicleRoute::class);
        return $data;
    }

    public function agent(){
        $data = $this->belongsTo(AdminAgent::class, 'agent_id', 'id');
        return $data;
    }
}
