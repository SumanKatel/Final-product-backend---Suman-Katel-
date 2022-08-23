<?php

namespace App\Model\admin;

use Illuminate\Database\Eloquent\Model;

class WishList extends Model {

    protected $table = 'wishlists';
    protected $guarded = ['id'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
