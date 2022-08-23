<?php

namespace App\Model\admin;

use Illuminate\Database\Eloquent\Model;

class ReceiverTerm extends Model {
    
    protected $table = 'rel_receiver_terms';
    protected $guarded = ['id'];    
}
