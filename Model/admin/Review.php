<?php

namespace App\Model\admin;

use Illuminate\Database\Eloquent\Model;

class Review extends Model {
    
    protected $table = 'reviews';
    protected $guarded = ['id'];    


  public function customer(){
        return $this->belongsTo('App\Model\admin\Customer','user_id','id');
    }
}
