<?php

namespace App\Http\Middleware;

use App\Model\admin\Customer;
use Closure;
use Illuminate\Http\Request;

class CustomerApiMiddleware
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
                $user = Customer::where('access_token', $key[1])->first();
                if(!empty($user)){
                    $request->request->add(['userid' => $user->id]);
                    \Auth::loginUsingId($user->id);
                } else{
                    return response()->json('Customer Not found !', 404);
                }
                
            }
        }

        return $next($request);
    }
}
