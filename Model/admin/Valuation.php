<?php

namespace App\Model\admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Valuation extends Model {

    protected $table = 'tbl_valuation';
    protected $guarded = ['id'];
}
