<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Route;
use Closure;
use JWTAuth;

class ApiAcl
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->user()->verifyRoute($request->path())) {
            return $next($request);
        }
        return response()->json(['success' => false, 'erros' => ['Unauthorized']], 401);
    }
    /*  public function handle($request, Closure $next, ...$parms)
    {
        $payload = JWTAuth::parseToken()->getPayload();
        $groups = $payload->get('groups');
       if(isset($groups)&& $groups !== null){
        foreach($parms as $parm){
            foreach($groups as $group){
                if(strpos($group, $parm) !== false){
                    return $next($request);
                }
            }
        }
       }
        
        return response()->json(['success' => false, 'erros' => ['Unauthorized']], 401);
    } */
}
