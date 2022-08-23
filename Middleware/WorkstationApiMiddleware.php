<?php

namespace App\Http\Middleware;

use App\Model\admin\Volunteer;
use Closure;
use Illuminate\Http\Request;

class WorkstationApiMiddleware
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
                $user = Volunteer::where('access_token', $key[1])->first();
                if(!empty($user)){
                    $request->request->add(['volunteerId' => $user->id]);
                    \Auth::loginUsingId($user->id);
                } else{
                    return response()->json([
                    'status'     => false,
                    'message'    => 'Volunteer Not Found',
                    'data'       =>  null,
                    ], 404);
                }
                
            }
        }

        return $next($request);
    }
}
