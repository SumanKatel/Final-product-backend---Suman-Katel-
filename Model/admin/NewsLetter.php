<?php

namespace App\Model\admin;

use Illuminate\Database\Eloquent\Model;

class NewsLetter extends Model {

    protected $table = 'tbl_newsletter_list';
    protected $guarded = ['id'];
}
