<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class BackgroundTheme extends Model
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

    protected $table = 'background_themes';
    
    protected $appends = [ 'is_image' ];

    public function getIsImageAttribute(){

        return config("constants.APP_IMAGE_URL").'uploads/background_themes/'.$this->image;
    }
   
}
