<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Http\Controllers\CommonController;
use App\Models\BackgroundTheme;

class Notifications extends Model
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
    protected $table = 'notifications';
	 
    protected $appends = [ 'post_posted_user', 'post_created_ago', 'post_category', 'post_theme', 'is_attachment', 'is_image_attachment', 'is_video_attachment', 'is_files_attachment' ]; 

    public function getIsAttachmentAttribute()
    {   $is_attachment = '';
        if(!empty($this->media_attachment)) {
            $is_attachment = config("constants.APP_IMAGE_URL").'uploads/media/'.$this->media_attachment;
        }   else {
            $is_attachment = '';
        }
        
        return $is_attachment;
    }

    public function getIsImageAttachmentAttribute()
    {   $is_image_attachment = [];
        if(!empty($this->image_attachment)) {
            $image_attachment = $this->image_attachment;

            if(!empty($image_attachment)) {
                $image_attachment = explode(',', $image_attachment);
                foreach($image_attachment as $img) {
                    $is_image_attachment[] = ['img' => config("constants.APP_IMAGE_URL").'uploads/media/'.$img];
                }
            } 
        }   else {
            $is_image_attachment = [];
        }
        
        return $is_image_attachment;
    }

    public function getIsVideoAttachmentAttribute()
    {   $is_video_attachment = '';
        if(!empty($this->video_attachment)) {
            $is_video_attachment = config("constants.APP_IMAGE_URL").'uploads/media/'.$this->video_attachment;
        }   else {
            $is_video_attachment = '';
        }
        
        return $is_video_attachment;
    }

    public function getIsFilesAttachmentAttribute()
    {   $is_files_attachment = [];
        if(!empty($this->files_attachment)) {
            $files_attachment = $this->files_attachment;

            if(!empty($files_attachment)) {
                $files_attachment = explode(',', $files_attachment);
                foreach($files_attachment as $img) {
                    $is_files_attachment[] = ['img' => config("constants.APP_IMAGE_URL").'uploads/media/'.$img];
                }
            } 
        }   else {
            $is_files_attachment = [];
        }
        
        return $is_files_attachment;
    }

    public function getPostPostedUserAttribute() { 

        $posted_user = User::where('id', $this->posted_by)
            ->select('users.id', 'users.name', 'users.profile_image', 'name_code')->first();  

        return $posted_user;
    }

    public function getPostCreatedAgoAttribute(){
        if(!empty($this->notify_datetime)) {
            return  CommonController::gettime_ago(strtotime($this->notify_datetime),1).' ago';
        }   else {
            return '';
        }
    } 

    public function getPostCategoryAttribute() { 

        $post_category = DB::table('categories')->where('id', $this->category_id)->value('name');  

        return $post_category;
    }

    public function getPostThemeAttribute() { 

        $post_theme = BackgroundTheme::where('id', $this->background_id)
            ->select('id', 'image')->first();  

        return $post_theme;
    }
    
}
