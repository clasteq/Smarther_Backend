<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Circulars extends Model
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
    protected $table = 'circular';
    
    protected $appends = ['is_circular_image', 'is_circular_attachments', 'is_circular_date','is_classname'];

    public function getIsCircularImageAttribute()    {
        if(!empty($this->circular_image)) {
            return config("constants.APP_IMAGE_URL").'uploads/circulars/'.$this->circular_image;
        }   else {
            return '';
        }
        
    } 

    public function getIsCircularAttachmentsAttribute()    {
        if(!empty($this->circular_attachments)) {
            return config("constants.APP_IMAGE_URL").'uploads/circulars/'.$this->circular_attachments;
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
        return date('d M Y', strtotime($this->circular_date));
    }

    public function getCircularDateAttribute($value) {
        return date('Y-m-d', strtotime($value));
    }
}
