<?php

namespace App\Model\admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class State extends Model {

    protected $table = 'tbl_state';
    protected $guarded = ['id'];
    public $timestamps = false;
}
