<?php

namespace App\Model\Front;

use Illuminate\Database\Eloquent\Model;

class ProductQuestionReply extends Model {
	
    protected $table = 'tbl_product_qes_ans';
    protected $guarded = ['id'];    
}
