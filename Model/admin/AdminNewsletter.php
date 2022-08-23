<?php

namespace App\Model\admin;

use Illuminate\Database\Eloquent\Model;

class AdminNewsletter extends Model {

    protected $table = 'tbl_newsletter_subscription';
    protected $guarded = ['id'];
}