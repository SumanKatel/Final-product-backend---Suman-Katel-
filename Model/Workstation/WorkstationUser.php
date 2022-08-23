<?php

namespace App\Model\Workstation;

use Illuminate\Database\Eloquent\Model;

class WorkstationUser extends Model {
	
    protected $table = 'tbl_workstation_user';
    protected $guarded = ['id'];    
}
