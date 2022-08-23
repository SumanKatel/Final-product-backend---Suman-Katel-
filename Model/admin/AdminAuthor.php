<?php

namespace App\Model\admin;

use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class AdminAuthor extends Model {
	
    protected $table = 'tbl_author';
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
