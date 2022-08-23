<?php

namespace App\Model\admin;

use Illuminate\Database\Eloquent\Model;

class ProductQuestionAnswer extends Model {
    
    protected $table = 'product_question_answers';
    protected $guarded = ['id'];
}
