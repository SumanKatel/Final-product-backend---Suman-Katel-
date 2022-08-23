<?php

namespace App\Model\Workstation;

use Illuminate\Database\Eloquent\Model;

class MinimumStock extends Model {
	
    protected $table = 'tbl_minimum_stock';
    protected $guarded = ['id'];    
}
