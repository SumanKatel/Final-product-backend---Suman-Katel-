<?php
namespace App\Model\site;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Pages extends Model {
    protected $table = 'tbl_pages';

    public static function getPageList(){
    	$fields = array(
			'slug',
			'title_LANG as title',
			'description_LANG as description',
		);
		$fields = str_replace('LANG', \App::getLocale(), $fields);
        $data = DB::table('tbl_pages')
        		->where('status','1')
                ->get($fields);
        return $data;
    }

    public static function getPageDetail($slug){
        $fields = array(
            'slug',
            'title_LANG as title',
            'description_LANG as description',
        );
        $fields = str_replace('LANG', \App::getLocale(), $fields);
        $data = DB::table('tbl_pages')
                ->where('slug',$slug)
                ->where('status','1')
                ->first($fields);
        return $data;
    }
}
