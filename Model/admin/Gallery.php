<?php

namespace App\Model\admin;

use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    protected $table = 'tbl_gallery';
    protected $guarded = ['id'];
}
