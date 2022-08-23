<?php

namespace App\Model\Workstation;

use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Employer extends Model {
	
    protected $table = 'tbl_loksewa_employer';
    protected $guarded = ['id'];    

    use HasSlug;

    public function getSlugOptions() : SlugOptions{
        return SlugOptions::create()
         		->generateSlugsFrom('name')
        		->saveSlugsTo('slug')
        		->doNotGenerateSlugsOnUpdate();
    }
}
