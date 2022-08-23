<?php

namespace App\Http\Middleware;

use App\Model\Workstation\WorkstationUser;
use Illuminate\Http\Request;
use Closure;


class WorkstationMiddleware
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
                $ws = WorkstationUser::where('access_token', $key[1])->first();
                if(!empty($ws)){
                    $request->request->add(['wsId' => $ws->id]);
                } else{
                    return response()->json([
                    'status'     => false,
                    'message'    => 'Workstation User Not Found',
                    'data'       =>  null,
                    ], 401);
                }
                
            }
        }

        return $next($request);
    }
}
