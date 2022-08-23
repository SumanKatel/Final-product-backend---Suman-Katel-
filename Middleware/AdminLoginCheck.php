<?php

namespace App\Http\Middleware;

use Closure;

class AdminLoginCheck {
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next){
        
        $value = $request->session()->get('admin');
        if (!$value) {
            return redirect(route('login.page'));
            // return redirect('my-admin/login');
        }

        return $next($request);
    }
}
