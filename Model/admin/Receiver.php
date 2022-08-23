<?php

namespace App\Model\admin;

use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Receiver extends Authenticatable {
    
    protected $table = 'tbl_receiver';
    protected $guarded = ['id'];    

    use HasSlug;

    public function getSlugOptions() : SlugOptions{
        return SlugOptions::create()
         		->generateSlugsFrom('patient_name')
        		->saveSlugsTo('slug')
        		->doNotGenerateSlugsOnUpdate();
    }
}
