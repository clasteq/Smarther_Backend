<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
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
    protected $table = 'banners';
    
    protected $appends = ['enc_id','is_image', 'pro_enc_id'];
    
   
    
    public function getIsImageAttribute(){

        return config("constants.APP_IMAGE_URL").'uploads/banners/'.$this->banner_image;
    }
    public function getEncIdAttribute()
    {
		$encodeid = $this->category_id; 
		$encode = base64_encode(json_encode(array('id'=>$encodeid)));
						
        return $encode;
    }
    public function getProEncIdAttribute()
    {
        $encodeid = $this->link_id; 
        if($encodeid > 0) {
            $encode = base64_encode(json_encode(array('id'=>$encodeid)));
        } else {
            $encode = '';
        }
        return $encode;
    }

}
