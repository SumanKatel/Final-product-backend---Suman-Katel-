<?php

namespace App\Model\admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class AdminPosts extends Model {

    protected $table = 'tbl_posts';
    protected $guarded = ['id'];

    use HasSlug;

    public function getSlugOptions() : SlugOptions{
        return SlugOptions::create()
                ->generateSlugsFrom('title')
                ->saveSlugsTo('slug')
                ->doNotGenerateSlugsOnUpdate();
    }
    

    public function category(){
    	$data = $this->belongsToMany(AdminCategory::class, 'rel_post_category', 'post_id', 'category_id');
    	return $data;
    }

    public function authordata(){
        $data = $this->hasOne(AdminAuthor::class, 'id', 'author_id')->select('id','title');
        return $data;
    }

    public static function deleteNewsCategoryList($id){
        $data = DB::table('rel_post_category')
                ->where('post_id', $id)
                ->delete();
        return $data;
    }

    public static function moduleUrl($table){
        $category=['tbl_posts' =>'detail','tbl_category' => 'category' , 'tbl_pages' => 'pages'];
        $data= DB::table($table)
                ->where('status','1')
                ->select('slug','title_en','title_np')
                ->get();
        return $data;
    }
}
