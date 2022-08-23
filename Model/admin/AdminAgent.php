<?php

namespace App\Model\admin;

use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Foundation\Auth\User as Authenticatable;


class AdminAgent extends Authenticatable
{
    protected $table = 'tbl_agent';
    protected $guarded = ['id'];
    protected $guard = 'agent';


    use HasSlug;

    public function getSlugOptions() : SlugOptions{
        return SlugOptions::create()
                ->generateSlugsFrom('name')
                ->saveSlugsTo('slug')
                ->doNotGenerateSlugsOnUpdate();
    }

    public function remittance(){
    	$data = $this->hasMany(AdminRemittance::class);
    	return $data;
    }

    public function vehicleRoute(){
        $data = $this->hasMany(AdminVehicleRoute::class);
        return $data;
    }

    public function vehicle(){
        $data = $this->hasMany(AdminVehicle::class);
        return $data;
    }
}
