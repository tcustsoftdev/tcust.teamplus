<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;

class CheckAdmin
{
    public static function canLogin($user)
    {
        if(!$user) return false;

        return true;
    }
    

    public function handle($request, Closure $next)
    {
        $user=request()->user();

        $can_login= static::canLogin($user);

        if($can_login) return $next($request);
        throw new AuthenticationException();
        
    }

    public static function exceptions($user=null)
    {
        
        if($user) auth()->logout();

        throw new AuthenticationException();
    }
    

    
}
