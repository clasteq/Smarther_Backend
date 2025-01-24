<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class CommunicationGroup extends Model
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
    protected $table = 'communication_groups';

    protected $appends = ['is_members'];

    public function getIsMembersAttribute() {
        $is_members = null;
        $members = $this->members;
        if(!empty($members)) {
            $members = explode(',', $members);
            $members = array_unique($members);
            $members = array_filter($members);
            if(count($members)>0) {
                $is_members = Student::leftjoin('users', 'users.id', 'students.user_id')
                    ->where('users.status','ACTIVE')->where('users.delete_status','0')
                    ->where('users.user_type','STUDENT')
                    ->whereIn('users.id',$members)
                    ->get(); 
            }
        }
        return $is_members;
    }
    
   
}
