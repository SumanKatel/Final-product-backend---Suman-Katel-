<?php

namespace App\Model\admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Hospital extends Model {

    protected $table = 'tbl_hospitals';
    protected $guarded = ['id'];
}
