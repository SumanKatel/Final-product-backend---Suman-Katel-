<?php

namespace App\Model\admin;

use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class TrainingDetail extends Model {

    protected $table = 'tbl_training_seminar';
    protected $guarded = ['id'];

    use HasSlug;

    public function getSlugOptions() : SlugOptions{
        return SlugOptions::create()
         		->generateSlugsFrom('title')
        		->saveSlugsTo('slug')
        		->doNotGenerateSlugsOnUpdate();
    }

    public function resourceperson(){
    	$data = $this->belongsToMany(ResourcePerson::class, 'rel_training_resource_person', 'training_id', 'resouce_person_id');
    	return $data;
    }

    public function coordinator(){
    	$data = $this->belongsToMany(TrainingCoOrdinator::class, 'rel_training_coordinator', 'training_id', 'coordinator_id');
        return $data;
    }

}
