<?php

namespace App\Http\Middleware;

use App\Model\admin\Receiver;
use Closure;
use Illuminate\Http\Request;

class ReceiverApiMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
        public function handle(Request $request, Closure $next)
    {

        if (!empty(getallheaders()['Authorization'])) {
            $key = explode(' ', getallheaders()['Authorization']);
            if (!empty($key[1])) {
                $user = Receiver::where('access_token', $key[1])->first();
                if(!empty($user)){
                    $request->request->add(['receiverId' => $user->id]);
                    \Auth::loginUsingId($user->id);
                } else{
                    return response()->json([
                    'status'     => 401,
                    'message'    => 'Receiver Not Found',
                    'data'       =>  null,
                    ], 200);
                }
                
            }
        }

        return $next($request);
    }
}
