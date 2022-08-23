<?php

namespace App\Model\Workstation;

use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class OrderPackage extends Model {
	
    protected $table = 'tbl_order_packaging';
    protected $guarded = ['id'];    
}
