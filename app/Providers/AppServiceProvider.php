<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $school = '';
        $ourl = URL('/'); 
        $url = URL('/'); 
        $curr = url()->full();
        $url = str_replace('/', '', $url);
        $curr = str_replace('/', '', $curr);
        $re = '/'.$url.'(.*)admin/'; 
        $str = $curr;

        preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);
 
        if(is_array($matches) && count($matches)>0) {
            $school = $matches[0][1];
        }

        \URL::forceRootUrl($ourl.'/'.$school);    

        $re = '/'.$url.'(.*)teacher/'; 
        $str = $curr;

        preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);
 
        if(is_array($matches) && count($matches)>0) {
            $school = $matches[0][1];
        }

        \URL::forceRootUrl($ourl.'/'.$school);    

        Paginator::useBootstrap();
    }
}
