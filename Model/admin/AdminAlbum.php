<?php

namespace App\Model\admin;

use App\Model\admin\AdminGallery;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use PrintHelper;

class AdminAlbum extends Model {

    protected $table = 'tbl_albums';
    protected $guarded = ['id'];

    public $upload = 'site/uploads/album/';
    public $thumb = 'site/uploads/album/thumb/';
    public $thumb2 = 'site/uploads/album/thumb2/';

    public function gallery() {
        return $this->hasMany(AdminGallery::class, 'parent_id');
    }

    public static function createModel() {
        $model = new AdminAlbum(Input::all());
        if (Input::hasFile('image')) {
            $extension = Input::file('image')->getClientOriginalExtension();
            $image = Str::slug(Input::get('title')) . time() . '.' . $extension;
            Input::file('image')->move(public_path($model->upload), $image);
            if (in_array(strtolower($extension), array('jpg', 'jpeg', 'gif', 'png'))) {
                Image::make(public_path($model->upload) . $image)->fit(400, 250)->save(public_path($model->thumb) . $image);
                Image::make(public_path($model->upload) . $image)->fit(300, 360)->save(public_path($model->thumb2) . $image);
            }
            $model->image = $image;
        }
        $model->slug = Str::slug(Input::get('title'));
        $model->sort_order = PrintHelper::nextSortOrder($model->table);
        $model->save();
    }

    public static function updateModel($id) {
        $inputs = Input::all();
        $model = AdminAlbum::findorfail($id);
        // $model->fill($inputs);
                
        if (Input::hasFile('image')) {
            if ($model->image) {
                File::delete(public_path($model->upload).$model->image);
                File::delete(public_path($model->thumb).$model->image);
                File::delete(public_path($model->thumb2).$model->image);
            }
            $extension = Input::file('image')->getClientOriginalExtension();
            $image = Str::slug(Input::get('title')).'-'.time().'.'.$extension;
            Input::file('image')->move(public_path($model->upload), $image);
            if (in_array(strtolower($extension), array('jpg', 'jpeg', 'gif', 'png'))) {
                Image::make(public_path($model->upload).$image)->fit(400, 250)->save(public_path($model->thumb) . $image);
                Image::make(public_path($model->upload).$image)->fit(300, 360)->save(public_path($model->thumb2) . $image);
            }
            $model->image = $image;
        }

        $model->slug = Str::slug(Input::get('title'));
        $model->save();
    }

    public static function deleteModel($id) {
        $crud = AdminAlbum::findorfail($id);
        if (!empty($crud)) {
            File::delete(public_path($crud->upload).$crud->image);
            File::delete(public_path($crud->thumb).$crud->image);
            File::delete(public_path($crud->thumb2).$crud->image);
            AdminGallery::deleteModelByParent($id);
        }
        return AdminAlbum::findorfail($id)->delete();
    }

}
