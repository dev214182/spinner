<?php

namespace App\Http\Middleware;

use Closure;
 
class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $roles)
    {
        
        if(!$roles){ return abort(404); }
        
        $roles = explode('|',$roles);

        
        foreach($roles as $role) {
            if($request->user()->hasRole($role)) { 
                return $next($request); 
            } 
        }   
        abort(404); 
       
        
    }
}
