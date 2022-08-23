<?php

namespace App\Model\admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Term extends Model {

    protected $table = 'tbl_terms';
    protected $guarded = ['id'];
}
