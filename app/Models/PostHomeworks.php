<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Http\Controllers\CommonController;

class PostHomeworks extends Model
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
    protected $table = 'homeworks';

    protected $appends = ['homeworks_list', 'posted_user', 'is_created_ago'];

    public function getHomeworksListAttribute()  {
        $homeworks_list = Homeworks::where('ref_no', $this->ref_no)->where('status', 'ACTIVE')->orderby('id','asc')->get();
        return $homeworks_list;
    }
    
    public function getPostedUserAttribute() { 

        $posted_user = DB::table('users')->where('id', $this->created_by)
            ->select('users.id', 'users.name', 'users.profile_image', 'name_code')->first(); 
        if(!empty($posted_user)) {
            $posted_user->is_profile_image = User::getUserProfileImageAttribute($this->created_by);
        } 

        return $posted_user;
    }

    public function getIsCreatedAgoAttribute(){
        if(!empty($this->created_at)) {
            return  CommonController::gettime_ago(strtotime($this->created_at),1).' ago';
        }   else {
            return '';
        }
    } 
}
