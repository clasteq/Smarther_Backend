<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subjects extends Model
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
    protected $table = 'subjects';

    protected $appends = [ 'is_subject_image', 'is_color_code'];

    public static $class_id;

    public function getIsSubjectImageAttribute()
    {
        $pictures =$this->subject_image;      
        $profile_picture = '';
        if (! empty($pictures)) {           
                $profile_picture = config("constants.APP_IMAGE_URL"). 'uploads/subjects/' . $this->subject_image;
            
        }else{
            $profile_picture = config("constants.APP_IMAGE_URL"). 'image/default.png';
		}
        return $profile_picture;
    }

    public function chapters() {
        return $this->hasMany('App\Models\Chapters','subject_id','id')->where('class_id', self::$class_id)
            ->where('status', 'ACTIVE')->orderby('position', 'asc');
    }

    public function topics() {
        return $this->hasMany('App\Models\Topics','subject_id','id')->where('topic_id', '>', 0)->orderby('position', 'asc');
    }

    public function books() {
        return $this->hasMany('App\Models\Topics','subject_id','id')->where('status', 'ACTIVE')
            ->where('class_id', self::$class_id)->orderby('position', 'asc');
    }

    public function getIsColorCodeAttribute() {
        return str_replace('#', '', $this->subject_colorcode);
    }
}
