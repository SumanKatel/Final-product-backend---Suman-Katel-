<?php

namespace App\Model\admin;

use Illuminate\Database\Eloquent\Model;

class ProductQuestion extends Model {
    
    protected $table = 'product_questions';
    protected $guarded = ['id'];

    public function customer(){
        return $this->belongsTo('App\Model\admin\Customer','user_id','id');
    }
}
