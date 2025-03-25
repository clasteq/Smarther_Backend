<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Category extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $table = 'categories';
    
    protected $appends = [ 'is_background_theme' ];

    public function getIsBackgroundThemeAttribute(){

        $is_background_theme = BackgroundTheme::where('id', $this->background_theme_id)->first();
        return $is_background_theme;
    }
}
