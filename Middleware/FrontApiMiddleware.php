<?php

namespace App\Http\Middleware;

use App\Model\Workstation\Customer;
use Illuminate\Http\Request;
use Closure;


class FrontApiMiddleware
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
                $customer = Customer::where('access_token', $key[1])->first();
                if(!empty($customer)){
                    $request->request->add(['customerId' => $customer->id]);
                } else{
                    return response()->json([
                    'status'     => false,
                    'message'    => 'Customer Not Found!!',
                    ], 401);
                }
                
            }
        }

        return $next($request);
    }
}
