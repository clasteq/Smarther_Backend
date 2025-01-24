<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Events extends Model
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
    protected $table = 'events';
    public $image;
    protected $appends = ['is_circular_image','is_circular_images', 'is_circular_date', 'is_circular_attachments', 'is_classname','is_circular_pics'];

    public function getIsCircularImageAttribute()    {
        if(!empty($this->circular_image)) {
            return config("constants.APP_IMAGE_URL").'uploads/circulars/'.$this->circular_image;
        }   else {
            return '';
        }
        
    } 

    // public function getIsCircularAttachmentsAttribute()    {
    //     if(!empty($this->circular_attachments)) {
    //         $arr = explode(';', $this->circular_attachments);
    //         $arr = array_unique($arr);
    //         $arr = array_filter($arr);
    //         if(count($arr)>0) {
    //             foreach($arr as $k => $v) {
    //                 $arr[$k]= config("constants.APP_IMAGE_URL").'uploads/circulars/'.$v;
    //             }
    //         } else { $arr = []; }
    //         return $arr;
    //     }   else {
    //         return '';
    //     }
        
    // } 


    
    public function getIsCircularImagesAttribute(){
        $val = [];
         if(!empty($this->circular_image)) {
        $mapped_test = $this->circular_image;
        $idsArr = explode(';',$mapped_test);
        $idsArr = array_unique($idsArr);
        $idsArr = array_filter($idsArr);
         foreach( $idsArr as $rowval){
           $val[]['images']= config("constants.APP_IMAGE_URL").'uploads/circulars/'.$rowval;
    
        }
    }
        // $this->image  = $val;
        return $val;



    }


    public function getIsCircularPicsAttribute(){
        $val = [];
         if(!empty($this->circular_image)) {
        $mapped_test = $this->circular_image;
        $idsArr = explode(';',$mapped_test);
        $idsArr = array_unique($idsArr);
        $idsArr = array_filter($idsArr);
         foreach( $idsArr as $rowval){
           $val[] = config("constants.APP_IMAGE_URL").'uploads/circulars/'.$rowval;
    
        }
    }
        // $this->image  = $val;
        return $val;



    }


    public function getIsImagesAttribute()
    {   $img = [];
        if(!empty($this->images)) {
            $imgs = explode(';', $this->images);
            $imgs = array_unique($imgs);
            $imgs = array_filter($imgs);
            foreach($imgs as $is) {
                $img[]['images'] = config("constants.APP_IMAGE_URL").'uploads/ads/'.$is;
            }
        }
        return $img;
    }

    public function getIsCircularAttachmentsAttribute()    {
        if(!empty($this->circular_attachments)) {
            $arr = explode(';', $this->circular_attachments);
            $arr = array_unique($arr);
            $arr = array_filter($arr);  $result='';
            if(count($arr)>0) {
                foreach($arr as $k => $v) {
                    $result= config("constants.APP_IMAGE_URL").'uploads/circulars/'.$v;
                }
            } else { $result= ''; }
            return $result;
        }   else {
            return '';
        }
    }



    public function getIsClassNameAttribute()    {
        if(!empty($this->class_ids)) {
            $arr = explode(',', $this->class_ids);
            $arr = array_unique($arr);
            $arr = array_filter($arr);
            $classnames = DB::table('classes')->whereIn('id', $arr)->select( DB::raw('group_concat(class_name) as is_classname'))->first();
            
            $is_classname='';
            if(!empty($classnames)>0) {  
                foreach($classnames as $k => $v) {
                    $is_classname = $v;
                }
            } else { $is_classname= ''; }
            return $is_classname;
        }   else {
            return '';
        }
    }

    public function getIsCircularDateAttribute() {
        return date('Y-m-d', strtotime($this->circular_date));
    }
}
