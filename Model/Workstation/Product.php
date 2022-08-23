<?php

namespace App\Model\Workstation;

use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Product extends Model {
	
    protected $table = 'tbl_product';
    protected $guarded = ['id']; 

    use HasSlug;

    public function getSlugOptions() : SlugOptions{
        return SlugOptions::create()
         		->generateSlugsFrom('product_name')
        		->saveSlugsTo('slug')
        		->doNotGenerateSlugsOnUpdate();
    } 
}
