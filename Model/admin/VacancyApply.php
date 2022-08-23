<?php

namespace App\Model\admin;

use Illuminate\Database\Eloquent\Model;

class VacancyApply extends Model
{
    protected $table='tbl_vacancy_apply';
    protected $guarded=['id'];

    
	public function job(){
        return $this->belongsTo('App\Model\admin\AdminJob','vacancy_id','id');
    }
}
