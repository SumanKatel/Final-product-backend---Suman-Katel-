<?php

namespace App\Model\Workstation;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model {
	
    protected $table = 'tbl_quiz';
    protected $guarded = ['id'];    
}
