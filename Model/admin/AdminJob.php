<?php

namespace App\Model\admin;

use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class AdminJob extends Model
{
    protected $table = 'tbl_jobs';
    protected $guarded = ['id'];

    use HasSlug;

    public function getSlugOptions() : SlugOptions{
        return SlugOptions::create()
                ->generateSlugsFrom('title')
                ->saveSlugsTo('slug')
                ->doNotGenerateSlugsOnUpdate();
    }
    public function apply(){
        return $this->hasMany('App\Model\admin\VacancyApply');
    }
}
