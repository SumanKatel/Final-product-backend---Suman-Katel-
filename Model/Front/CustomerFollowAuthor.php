<?php

namespace App\Model\Front;

use Illuminate\Database\Eloquent\Model;

class CustomerFollowAuthor extends Model {
	
    protected $table = 'tbl_customer_author_follow';
    protected $guarded = ['id'];    
}
