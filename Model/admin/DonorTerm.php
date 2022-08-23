<?php

namespace App\Model\admin;

use Illuminate\Database\Eloquent\Model;

class DonorTerm extends Model {
    
    protected $table = 'rel_donor_terms';
    protected $guarded = ['id'];    
}
