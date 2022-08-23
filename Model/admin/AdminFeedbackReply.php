<?php

namespace App\Model\admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AdminFeedbackReply extends Model {

    protected $table = 'tbl_feedback_reply';
    protected $guarded = ['id'];
}
