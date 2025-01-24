<?php

namespace App\Http\Helpers;

class Helpers {

    /**
     * Generate a url for the application.
     *
     * @param  string  $path
     * @param  mixed   $parameters
     * @param  bool    $secure
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    public static function url($path = null, $parameters = [], $secure = null) {
        $path = (string) $path;
        if (strlen($path) > 0 && $path[0] !== '/') {
            $path = '/' . $path;
        }
        return url(app()->getLocale() . $path, $parameters, $secure);
    }

    /**
     * Generate a HTTPS url for the application.
     *
     * @param  string  $path
     * @param  mixed   $parameters
     * @return string
     */
    public static function secure_url($path, $parameters = []) {
        return static::url($path, $parameters, true);
    }

}