<?php

namespace App\Model\Workstation;

use Illuminate\Database\Eloquent\Model;

class PhotoSliderPostDetail extends Model {
	
    protected $table = 'tbl_photo_slider_post_detail';
    protected $guarded = ['id']; 
    public $timestamps = true;   
}
