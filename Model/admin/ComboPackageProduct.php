<?php

namespace App\Model\admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ComboPackageProduct extends Model {

    protected $table = 'tbl_combo_package_product';
    protected $guarded = ['id'];
}
