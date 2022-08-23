<?php

namespace App\Model\admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Recharge extends Model {

    protected $table = 'tbl_recharge';
    protected $guarded = ['id'];
}
