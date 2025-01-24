<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class OASections extends Model
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
    protected $table = 'sections'; 

    protected $appends = ['boys','girls','total', 'academic_year', 'attendance', 'is_approved'];

    protected $boys;
    protected $girls;

    public static $cdate; 
    public static $acadamic_year; 

    public function getIsApprovedAttribute() {
        $cdate =  (self::$cdate) ? (self::$cdate) : date('Y-m-d');
        $approved = DB::table('attendance_approval_class_section')->whereDate('date', $cdate)
            ->where('class_id', $this->class_id)->where('section_id', $this->id)
            ->where('admin_status',1)->first();
        if(!empty($approved)) {
            $is_approved = "Approved";
        } else {
            $is_approved = "Un Approved";
        }
        return $is_approved;
    }

    public function getBoysAttribute() {
        if(self::$acadamic_year) {
            $acadamic_year = self::$acadamic_year;
        } else {
            $settings = DB::table('admin_settings')->orderby('id', 'asc')->first();
            $acadamic_year = $settings->acadamic_year;
        }
        $boys = 0;
        $boys_cnt = DB::table('student_class_mappings')
            ->leftjoin('users', 'users.id', 'student_class_mappings.user_id')
            ->where('student_class_mappings.academic_year', $acadamic_year)
            ->where('student_class_mappings.class_id', $this->class_id)
            ->where('student_class_mappings.section_id', $this->id)
            ->where('users.status', 'ACTIVE')->where('users.gender', 'MALE')->where('users.delete_status',0)
            ->select('users.id')->get();
        if($boys_cnt->isNotEmpty()) {
            $boys = count($boys_cnt);
        }
        $this->boys = $boys;
        return $boys;
    }

    public function getGirlsAttribute() {
        if(self::$acadamic_year) {
            $acadamic_year = self::$acadamic_year;
        } else {
            $settings = DB::table('admin_settings')->orderby('id', 'asc')->first();
            $acadamic_year = $settings->acadamic_year;
        }
        $girls = 0;
        $girls_cnt = DB::table('student_class_mappings')
            ->leftjoin('users', 'users.id', 'student_class_mappings.user_id')
            ->where('student_class_mappings.academic_year', $acadamic_year)
            ->where('student_class_mappings.class_id', $this->class_id)
            ->where('student_class_mappings.section_id', $this->id)
            ->where('users.status', 'ACTIVE')->where('users.gender', 'FEMALE')->where('users.delete_status',0)
            ->select('users.id')->get();
        if($girls_cnt->isNotEmpty()) {
            $girls = count($girls_cnt);
        }
        $this->girls = $girls;
        return $girls;
    }

    public function getTotalAttribute() {
        $total = 0; 
        $total = $this->boys + $this->girls;
        return $total;
    }

    public function getAcademicYearAttribute() {
        $academic_year = '';
        if(self::$acadamic_year) {
            $academic_year = self::$acadamic_year;
        } else {
            $settings = DB::table('admin_settings')->orderby('id', 'asc')->first();
            $academic_year = $settings->acadamic_year;
        }
        return $academic_year;
    }

    public function getAttendanceAttribute() {
        if(self::$acadamic_year) {
            $acadamic_year = self::$acadamic_year;
        } else {
            $settings = DB::table('admin_settings')->orderby('id', 'asc')->first();
            $acadamic_year = $settings->acadamic_year;
        }
        $cdate =  (self::$cdate) ? (self::$cdate) : date('Y-m-d');
        $cday = 'day_'.date('j', strtotime($cdate));   $cday_an = 'day_'.date('j', strtotime($cdate)).'_an'; 
        $month_year = date('Y-m', strtotime($cdate));

        $oa_boys = DB::table('student_class_mappings')
            ->leftjoin('users', 'users.id', 'student_class_mappings.user_id')
            ->where('student_class_mappings.academic_year', $acadamic_year) 
            ->where('users.status', 'ACTIVE')->where('users.gender', 'MALE')->where('users.delete_status',0) 
            ->where('student_class_mappings.class_id', $this->class_id)
            ->where('student_class_mappings.section_id', $this->id)
            ->select('users.id')->count();  

        $oa_girls = DB::table('student_class_mappings')
            ->leftjoin('users', 'users.id', 'student_class_mappings.user_id')
            ->where('student_class_mappings.academic_year', $acadamic_year) 
            ->where('users.status', 'ACTIVE')->where('users.gender', 'FEMALE')->where('users.delete_status',0) 
            ->where('student_class_mappings.class_id', $this->class_id)
            ->where('student_class_mappings.section_id', $this->id)
            ->select('users.id')->count(); 

        $total = $oa_boys + $oa_girls;

        $att_bp_fn = DB::table('studentsdaily_attendance')->leftjoin('users', 'users.id', 'studentsdaily_attendance.user_id')
            ->where('monthyear', $month_year)->where($cday, 1)->where('user_type', 'STUDENT')
            ->where('users.delete_status',0)->where('gender', 'MALE') 
            ->where('studentsdaily_attendance.class_id', $this->class_id)
            ->where('studentsdaily_attendance.section_id', $this->id)
            ->select('users.id')->count();
        $att_bp_an = DB::table('studentsdaily_attendance')->leftjoin('users', 'users.id', 'studentsdaily_attendance.user_id')
            ->where('monthyear', $month_year)->where($cday_an, 1)->where('user_type', 'STUDENT')
            ->where('users.delete_status',0)->where('gender', 'MALE') 
            ->where('studentsdaily_attendance.class_id', $this->class_id)
            ->where('studentsdaily_attendance.section_id', $this->id)
            ->select('users.id')->count();

        $att_ba_fn = $this->boys - $att_bp_fn;
        $att_ba_an = $this->boys - $att_bp_an;
        if($att_ba_fn < 0) { $att_ba_fn = 0; }
        if($att_ba_an < 0) { $att_ba_an = 0; }

        $tot_b_fn = $att_bp_fn + $att_ba_fn;
        $tot_b_an = $att_bp_an + $att_ba_an;

        $att_gp_fn = DB::table('studentsdaily_attendance')->leftjoin('users', 'users.id', 'studentsdaily_attendance.user_id')
            ->where('monthyear', $month_year)->where($cday, 1)->where('user_type', 'STUDENT')
            ->where('users.delete_status',0)->where('gender', 'FEMALE') 
            ->where('studentsdaily_attendance.class_id', $this->class_id)
            ->where('studentsdaily_attendance.section_id', $this->id)
            ->select('users.id')->count();
        $att_gp_an = DB::table('studentsdaily_attendance')->leftjoin('users', 'users.id', 'studentsdaily_attendance.user_id')
            ->where('monthyear', $month_year)->where($cday_an, 1)->where('user_type', 'STUDENT')
            ->where('users.delete_status',0)->where('gender', 'FEMALE') 
            ->where('studentsdaily_attendance.class_id', $this->class_id)
            ->where('studentsdaily_attendance.section_id', $this->id)
            ->select('users.id')->count();

        $att_ga_fn = $this->girls - $att_gp_fn;
        $att_ga_an = $this->girls - $att_gp_an; 
        if($att_ga_fn < 0) { $att_ga_fn = 0; }
        if($att_ga_an < 0) { $att_ga_an = 0; }
        
        $tot_g_fn = $att_gp_fn + $att_ga_fn;
        $tot_g_an = $att_gp_an + $att_ga_an;

        $att_oap_fn = $att_bp_fn + $att_gp_fn;
        $att_oap_an = $att_bp_an + $att_gp_an;
        $att_oaa_fn = $att_ba_fn + $att_ga_fn;
        $att_oaa_an = $att_ba_an + $att_ga_an;  

        $tot_p_fn = $att_bp_fn + $att_gp_fn;
        $tot_p_an = $att_bp_an + $att_gp_an;
        $tot_a_fn = $att_ba_fn + $att_ga_fn;
        $tot_a_an = $att_ba_an + $att_ga_an;

        return array('att_bp_fn'=>$att_bp_fn, 'att_bp_an' => $att_bp_an, 
                'att_ba_fn'=>$att_ba_fn, 'att_ba_an' => $att_ba_an, 
                'att_gp_fn' =>$att_gp_fn, 'att_gp_an'=>$att_gp_an, 
                'att_ga_fn' =>$att_ga_fn, 'att_ga_an'=>$att_ga_an, 
                'att_oap_fn' =>$att_oap_fn, 'att_oap_an'=>$att_oap_an, 
                'att_oaa_fn' =>$att_oaa_fn, 'att_oaa_an'=>$att_oaa_an, 
                'total' =>$total, 'oa_boys'=>$oa_boys, 'oa_girls'=>$oa_girls,
                'tot_b_fn' =>$tot_b_fn, 'tot_b_an'=>$tot_b_an, 
                'tot_g_fn' =>$tot_g_fn, 'tot_g_an'=>$tot_g_an,
                'tot_p_fn' =>$tot_p_fn, 'tot_p_an'=>$tot_p_an, 
                'tot_a_fn' =>$tot_a_fn, 'tot_a_an'=>$tot_a_an, );

    }

    public static function getOverallAttribute($acadamic_year, $school_id) {

        if(self::$acadamic_year) {
            $acadamic_year = self::$acadamic_year;
        } else {
            /*$settings = DB::table('admin_settings')->orderby('id', 'asc')->first();
            $acadamic_year = $settings->acadamic_year;*/ 
        }
        $cdate =  (self::$cdate) ? (self::$cdate) : date('Y-m-d');
        $cday = 'day_'.date('j', strtotime($cdate));   $cday_an = 'day_'.date('j', strtotime($cdate)).'_an'; 
        $monthyear = date('Y-m', strtotime($cdate));

        $oa_students = DB::table('student_class_mappings')
                ->leftjoin('users', 'users.id', 'student_class_mappings.user_id')
                ->where('student_class_mappings.academic_year', $acadamic_year) 
                ->where('users.delete_status',0)->where('users.status', 'ACTIVE')
                ->where('users.school_college_id', $school_id)
                ->select('users.id')->count();

        $oa_boys = DB::table('student_class_mappings')
        ->leftjoin('users', 'users.id', 'student_class_mappings.user_id')
        ->where('student_class_mappings.academic_year', $acadamic_year)->where('users.school_college_id', $school_id) 
        ->where('users.status', 'ACTIVE')->where('users.gender', 'MALE')->where('users.delete_status',0)
        ->select('users.id')->count();  

        $oa_girls = DB::table('student_class_mappings')
        ->leftjoin('users', 'users.id', 'student_class_mappings.user_id')
        ->where('student_class_mappings.academic_year', $acadamic_year)->where('users.school_college_id', $school_id) 
        ->where('users.status', 'ACTIVE')->where('users.gender', 'FEMALE')->where('users.delete_status',0)
        ->select('users.id')->count(); 

        //$cday = 'day_'.date('j');   $cday_an = 'day_'.date('j').'_an';

        $att_bp_fn = DB::table('studentsdaily_attendance')->leftjoin('users', 'users.id', 'studentsdaily_attendance.user_id')
            ->where('monthyear', $monthyear)->where($cday, 1)->where('user_type', 'STUDENT')
            ->where('users.school_college_id', $school_id)
            ->where('users.delete_status',0)->where('gender', 'MALE') 
            ->select('users.id')->count();
        $att_bp_an = DB::table('studentsdaily_attendance')->leftjoin('users', 'users.id', 'studentsdaily_attendance.user_id')
            ->where('monthyear', $monthyear)->where($cday_an, 1)->where('user_type', 'STUDENT')
            ->where('users.school_college_id', $school_id)
            ->where('users.delete_status',0)->where('gender', 'MALE') 
            ->select('users.id')->count();

        $att_ba_fn = $oa_boys - $att_bp_fn;
        $att_ba_an = $oa_boys - $att_bp_an;

        $att_gp_fn = DB::table('studentsdaily_attendance')->leftjoin('users', 'users.id', 'studentsdaily_attendance.user_id')
            ->where('monthyear', $monthyear)->where($cday, 1)->where('user_type', 'STUDENT')
            ->where('users.school_college_id', $school_id)
            ->where('users.delete_status',0)->where('gender', 'FEMALE') 
            ->select('users.id')->count();
        $att_gp_an = DB::table('studentsdaily_attendance')->leftjoin('users', 'users.id', 'studentsdaily_attendance.user_id')
            ->where('monthyear', $monthyear)->where($cday_an, 1)->where('user_type', 'STUDENT')
            ->where('users.school_college_id', $school_id)
            ->where('users.delete_status',0)->where('gender', 'FEMALE') 
            ->select('users.id')->count();

        $att_ga_fn = $oa_girls - $att_gp_fn;
        $att_ga_an = $oa_girls - $att_gp_an; 

        $att_oap_fn = $att_bp_fn + $att_gp_fn;
        $att_oap_an = $att_bp_an + $att_gp_an;
        $att_oaa_fn = $att_ba_fn + $att_ga_fn;
        $att_oaa_an = $att_ba_an + $att_ga_an; 

        return [ 
            'oa_students'=>$oa_students, 'oa_boys'=>$oa_boys, 'oa_girls'=>$oa_girls, 
            'att_bp_fn'=>$att_bp_fn, 'att_bp_an' => $att_bp_an, 
            'att_ba_fn'=>$att_ba_fn, 'att_ba_an' => $att_ba_an, 
            'att_gp_fn' =>$att_gp_fn, 'att_gp_an'=>$att_gp_an, 
            'att_ga_fn' =>$att_ga_fn, 'att_ga_an'=>$att_ga_an, 
            'att_oap_fn' =>$att_oap_fn, 'att_oap_an'=>$att_oap_an, 
            'att_oaa_fn' =>$att_oaa_fn, 'att_oaa_an'=>$att_oaa_an, 
        ];
    }


}
