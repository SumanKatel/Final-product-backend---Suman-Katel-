<?php

namespace App\Model\admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class HospitalCoordinator extends Model {

    protected $table = 'tbl_hospital_cordinators';
    protected $guarded = ['id'];
}
