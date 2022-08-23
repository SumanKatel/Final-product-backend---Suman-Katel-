<?php

namespace App\Model\Workstation;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model {
	
    protected $table = 'tbl_payment_method';
    protected $guarded = ['id'];    
}
