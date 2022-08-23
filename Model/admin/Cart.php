<?php

namespace App\Model\admin;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model {

    protected $table = 'carts';
    protected $guarded = ['id'];

     public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
