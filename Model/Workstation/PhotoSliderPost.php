<?php

namespace App\Model\Workstation;

use Illuminate\Database\Eloquent\Model;

class PhotoSliderPost extends Model {
	
    protected $table = 'tbl_photo_slider_post';
    protected $guarded = ['id']; 
    public $timestamps = true;   
}
