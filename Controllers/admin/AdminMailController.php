<?php

namespace App\Http\Controllers\admin;
use App\Http\Controllers\admin\AdminController;
use App\Model\admin\AdminChefDetail;
use Illuminate\Mail\Message;
use Mail;

class AdminMailController extends AdminController {

    public static function sendGenerateLoginDetailMail($chefid,$password){
        $chefdetail = AdminChefDetail::findOrFail($chefid);
        // return $chefdetail;

        $data = array (
            'subject'           => 'SajiloMeals Chef Login Credentials',
            'chefdetail'        => $chefdetail,
            'password'        	=> $password,
        );
        // return view('email.generatelogindetail', $data);
        Mail::send('email.generatelogindetail', $data, function (Message $mail)use ($data) {
            $mail_from = env('MAIL_USERNAME', 'ninzajnivaj@gmail.com');
            $mail->from($mail_from, 'SajiloMeals');
            $mail->to($data['chefdetail']->email, $data['chefdetail']->email);
            $mail->subject($data['subject']);
            $mail->priority(3);
        });
	}

}
