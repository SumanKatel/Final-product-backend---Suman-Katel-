<?php

namespace App\Model\admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ProductCategory extends Model {

    protected $table = 'tbl_product_category';
    protected $guarded = ['id'];
}
