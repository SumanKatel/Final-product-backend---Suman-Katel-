<?php

namespace App\Model\Workstation;

use Illuminate\Database\Eloquent\Model;

class BillType extends Model {
	
    protected $table = 'tbl_bill_type';
    protected $guarded = ['id'];    
}
