<?php

namespace App\Model\Workstation;

use Illuminate\Database\Eloquent\Model;

class Country extends Model {
	
    protected $table = 'tbl_country';
    protected $guarded = ['id'];    
}
