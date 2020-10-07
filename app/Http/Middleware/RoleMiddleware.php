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
    public function handle($request, Closure $next, ...$roles)
    {

        
        if(empty($roles)) $roles = [1];
        foreach($roles as $role) {
            if($request->user()->role == $role) { 
                return $next($request); 
            } 
        } 
        return abort(404);

    }
}
