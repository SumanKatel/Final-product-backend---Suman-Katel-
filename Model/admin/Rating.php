<?php

namespace App\Model\admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Rating extends Model {

    protected $table = 'tbl_rating';
    protected $guarded = ['id'];

}
