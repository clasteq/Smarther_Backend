<?php


namespace App\Http\Middleware;


use Closure;


class IdentifyCustomer
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
       // echo $request->url();  echo $request->fullUrl();
    }
}