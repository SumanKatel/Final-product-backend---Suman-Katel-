<?php

namespace App\Model\admin;

use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Product extends Model {
	
    protected $table = 'tbl_product';
    protected $guarded = ['id'];    

    use HasSlug;
    public function getSlugOptions() : SlugOptions{
        return SlugOptions::create()
         		->generateSlugsFrom('title')
        		->saveSlugsTo('slug')
        		->doNotGenerateSlugsOnUpdate();
    }

     public function cat(){
        return $this->belongsTo('App\Model\admin\Categories','category_id','id');
    }

     public function brand(){
        return $this->belongsTo('App\Model\admin\Brand','brand_id','id');
    }

    public function vendor(){
        return $this->belongsTo('App\Model\admin\Vendor','vendor_id','id');
    }
    // public function category(){
    //     return $this->belongsTo('App\Model\admin\ProductCategory','product_category_id','id');
    // }


    public static function makeCombinations($arrays) {
        $result = array(array());
        if($arrays[0]!==null)
        {
        foreach ($arrays as $property => $property_values) {
            $tmp = array();
            foreach ($result as $result_item) {
                foreach ($property_values as $property_value) {
                    $tmp[] = array_merge($result_item, array($property => $property_value));
                }
            }
            $result = $tmp;
        }
        return $result;
        }
    }
}
