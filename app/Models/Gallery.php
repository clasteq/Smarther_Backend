<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Gallery extends Model
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
    protected $table = 'gallery';

    protected $appends = [ 'is_gallery_images' ];

    public function getIsGalleryImagesAttribute(){
        $val = [];
        if(!empty($this->gallery_image)) {
            $mapped_test = $this->gallery_image;
            $idsArr = explode(';',$mapped_test);
            $idsArr = array_unique($idsArr);
            $idsArr = array_filter($idsArr);
             foreach( $idsArr as $rowval){
               $val[]['images']= config("constants.APP_IMAGE_URL").'uploads/gallery/'.$this->school_id.'/'.$rowval;
        
            }
        } 
        return $val; 
    }

}