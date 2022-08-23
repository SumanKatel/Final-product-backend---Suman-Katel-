<?php

namespace App\Model\admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Booked extends Model {

    protected $table = 'tbl_booked';
    protected $guarded = ['id'];
}
