<?php

namespace App\Model\admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class ComboPackage extends Model {

    protected $table = 'tbl_combo_package';
    protected $guarded = ['id'];

    use HasSlug;

    public function getSlugOptions() : SlugOptions{
        return SlugOptions::create()
         		->generateSlugsFrom('title')
        		->saveSlugsTo('slug')
        		->doNotGenerateSlugsOnUpdate();
    }
}
