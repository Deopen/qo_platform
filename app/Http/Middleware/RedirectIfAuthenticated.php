<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\User;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $access_level="";
        //avoid to get register page if not admin
        if (Auth::guard($guard)->check()) {
            $access_level=Auth::user()->access_level;
                if ($access_level!="admin" and $access_level!="project_owner")
                    return redirect('/');
        }else{
        	return $next($request);
        }
        
        if ($request->path()=="register"){
            
            if ($access_level=="admin" or $access_level=="project_owner")
                return $next($request);
            else
                return redirect('/login');        
        }else{
            return $next($request);
        }//end if req
    }//end function handle
}
