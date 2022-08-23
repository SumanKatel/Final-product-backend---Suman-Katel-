<?php

namespace App\Model\admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Municipality extends Model {

    protected $table = 'tbl_municipality';
    protected $guarded = ['id'];
    public $timestamps = false;
}
