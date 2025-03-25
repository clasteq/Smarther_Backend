<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

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

    protected $appends = ['is_members', 'is_staff_members'];

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
    
    public function getIsStaffMembersAttribute() {
        $is_staff_members = null;
        $staff_members = $this->staff_members;
        if(!empty($staff_members)) {
            $staff_members = explode(',', $staff_members);
            $staff_members = array_unique($staff_members);
            $staff_members = array_filter($staff_members);
            if(count($staff_members)>0) {
                $is_staff_members = DB::table('teachers')->leftjoin('users', 'users.id', 'teachers.user_id')
                    ->where('users.status','ACTIVE')->where('users.delete_status','0')
                    ->where('users.user_type','TEACHER')
                    ->whereIn('users.id',$staff_members)
                    ->select('users.id', 'users.name')
                    ->get(); 
            }
        }
        return $is_staff_members;
    }
    
}
