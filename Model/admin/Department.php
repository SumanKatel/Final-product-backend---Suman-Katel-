<?php

namespace App\Model\admin;

use Illuminate\Database\Eloquent\Model;

class Department extends Model {
    
    protected $table = 'tbl_department';
    protected $guarded = ['id'];

    public function team(){
        return $this->hasMany('App\Model\admin\Team');
    }
}
