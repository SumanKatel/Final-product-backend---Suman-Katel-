<?php

namespace App\Model\admin;

use Illuminate\Database\Eloquent\Model;

class AdminVehicleRoute extends Model
{
    protected $table = 'tbl_vehicle_route';
    protected $guarded = ['id'];

    public function agent(){
        $data = $this->belongsTo(AdminAgent::class, 'agent_id', 'id');
        return $data;
    }

    public function vehicle(){
        $data = $this->belongsTo(AdminVehicle::class, 'vehicle_id', 'id');
        return $data;
    }

    public function addressFrom(){
        $data = $this->belongsTo(AdminRouteAddress::class, 'address_from_id', 'id');
        return $data;
    }

    public function addressTo(){
        $data = $this->belongsTo(AdminRouteAddress::class, 'address_to_id', 'id');
        return $data;
    }
}
