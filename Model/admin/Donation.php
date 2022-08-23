<?php

namespace App\Model\admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Donation extends Model {

    protected $table = 'tbl_donation';
    protected $guarded = ['id'];
}
