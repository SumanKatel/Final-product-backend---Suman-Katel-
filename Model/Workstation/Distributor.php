<?php

namespace App\Model\Workstation;

use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Distributor extends Model {
	
    protected $table = 'tbl_distributor';
    protected $guarded = ['id'];   

    use HasSlug;

    public function getSlugOptions() : SlugOptions{
        return SlugOptions::create()
         		->generateSlugsFrom('title')
        		->saveSlugsTo('slug')
        		->doNotGenerateSlugsOnUpdate();
    } 
}
