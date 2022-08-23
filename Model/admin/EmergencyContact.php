<?php

namespace App\Model\admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EmergencyContact extends Model {

    protected $table = 'tbl_emergency_contact';
    protected $guarded = ['id'];
}
