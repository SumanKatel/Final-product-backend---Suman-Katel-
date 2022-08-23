<?php

namespace App\Model\Workstation;

use Illuminate\Database\Eloquent\Model;

class QuizAnswer extends Model {
	
    protected $table = 'tbl_quiz_answer';
    protected $guarded = ['id'];    
}
