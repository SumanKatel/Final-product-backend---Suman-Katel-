<?php

namespace App\Model\admin;

use Illuminate\Database\Eloquent\Model;

class Partner extends Model {

    protected $table = 'tbl_partner';
    protected $guarded = ['id'];

    public function partnertype(){
         return $this->hasOne(PartnerType::class,'id','partner_type_id')->select('id','title');
    }
}
