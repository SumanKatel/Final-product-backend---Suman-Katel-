<?php

namespace App\Model\admin;

use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class BannerDownload extends Model {
	
    protected $table = 'brochure_download';
    protected $guarded = ['id'];    
}
