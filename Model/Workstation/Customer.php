<?php

namespace App\Model\Workstation;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model {
	
    protected $table = 'customers';
    protected $guarded = ['id'];    

    use HasSlug;

    public function getSlugOptions() : SlugOptions{
        return SlugOptions::create()
         		->generateSlugsFrom('name')
        		->saveSlugsTo('slug')
        		->doNotGenerateSlugsOnUpdate();
    }
}
