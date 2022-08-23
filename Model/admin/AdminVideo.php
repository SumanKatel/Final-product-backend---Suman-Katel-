<?php

namespace App\Model\admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use PrintHelper;

class AdminVideo extends Model { 

    protected $table = 'tbl_videos';
    protected $guarded = ['id'];

}
