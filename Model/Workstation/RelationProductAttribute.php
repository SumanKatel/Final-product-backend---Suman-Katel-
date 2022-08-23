<?php

namespace App\Model\Workstation;

use Illuminate\Database\Eloquent\Model;

class RelationProductAttribute extends Model {
	
    protected $table = 'product_attribute';
    protected $guarded = ['id'];    
}
