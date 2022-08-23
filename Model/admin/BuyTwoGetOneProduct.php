<?php

namespace App\Model\admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BuyTwoGetOneProduct extends Model {

    protected $table = 'tbl_buy_two_product';
    protected $guarded = ['id'];
}
