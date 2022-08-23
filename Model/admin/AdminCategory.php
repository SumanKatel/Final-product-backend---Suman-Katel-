<?php

namespace App\Model\admin;

use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class AdminCategory extends Model {
	
    protected $table = 'tbl_category';
    protected $guarded = ['id'];    

    use HasSlug;

    public function getSlugOptions() : SlugOptions{
        return SlugOptions::create()
         		->generateSlugsFrom('title')
        		->saveSlugsTo('slug')
        		->doNotGenerateSlugsOnUpdate();
    }

   	public function posts(){
   		return $this->belongsToMany(AdminPosts::Class, 'rel_post_category', 'category_id', 'post_id');
    }
}
