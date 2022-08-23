<?php

namespace App\Model\admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Symptom extends Model {

    protected $table = 'tbl_symptoms';
    protected $guarded = ['id'];

    public function cat(){
        return $this->belongsTo(SymptomCategory::class,'symptoms_category_id','id');
    }
}
