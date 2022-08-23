<?php

namespace App\Model\admin;

use Illuminate\Database\Eloquent\Model;

class AdminRouteAddress extends Model
{
    protected $table = 'tbl_route_address';
    protected $guarded = ['id'];

    public function vehicleRouteFrom(){
        $data = $this->hasMany(AdminVehicleRoute::class, 'address_from_id', 'id');
        return $data;
    }

    public function vehicleRouteTo(){
        $data = $this->hasMany(AdminVehicleRoute::class, 'address_to_id', 'id');
        return $data;
    }    
}
