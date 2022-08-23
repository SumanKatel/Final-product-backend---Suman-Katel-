<?php

namespace App\Model\admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ConfirmReceiver extends Model {

    protected $table = 'tbl_confirm_receive';
    protected $guarded = ['id'];
}
