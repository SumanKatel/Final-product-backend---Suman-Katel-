<?php

namespace App\Model\site;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Nivaj extends Model {
    
    public static function getCacheMethodData($name){
    	$data = DB::table('tbl_iporesult_1')
    			->where('name', 'like', $name.'%')
    			->get();
    	return $data;
    }
}
