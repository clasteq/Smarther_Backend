<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Http\Controllers\CommonController;

class OAFeesSections extends Model
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

    protected $appends = [ 'total_scholars', 'total_fees', 'total_collected', 'total_concession', 'total_balance', 'paid_percentage']; 
    public static $acadamic_year; 

    public function getTotalScholarsAttribute() {

        $total_scholars = 0; 

        if(self::$acadamic_year) {
            $acadamic_year = self::$acadamic_year;
        } else {
            $settings = DB::table('admin_settings')->orderby('id', 'asc')->first();
            $acadamic_year = $settings->acadamic_year;
        }
        $boys = 0; $girls = 0;
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

        $total_scholars = $boys + $girls;
        return $total_scholars;
    }

    public function getTotalFeesAttribute() {
        $total_fees = 0;

        if(self::$acadamic_year) {
            $acadamic_year = self::$acadamic_year;
        } else {
            $settings = DB::table('admin_settings')->orderby('id', 'asc')->first();
            $acadamic_year = $settings->acadamic_year;
        }

        $total_fees = DB::table('student_class_mappings')->leftjoin('users', 'users.id', 'student_class_mappings.user_id')   
            ->where('student_class_mappings.academic_year', $acadamic_year)
            ->where('student_class_mappings.class_id', $this->class_id)
            ->where('student_class_mappings.section_id', $this->id) 
            ->where('users.delete_status',0)->where('users.status','ACTIVE') 
            ->sum('student_class_mappings.total_fees'); 

        if($total_fees > 0) { } else { $total_fees = 0; }

        return CommonController::price_format($total_fees);
    }

    public function getTotalCollectedAttribute() {
        $total_collected = 0;

        if(self::$acadamic_year) {
            $acadamic_year = self::$acadamic_year;
        } else {
            $settings = DB::table('admin_settings')->orderby('id', 'asc')->first();
            $acadamic_year = $settings->acadamic_year;
        }

        $total_collected = DB::table('student_class_mappings')->leftjoin('users', 'users.id', 'student_class_mappings.user_id')   
            ->where('student_class_mappings.academic_year', $acadamic_year)
            ->where('student_class_mappings.class_id', $this->class_id)
            ->where('student_class_mappings.section_id', $this->id) 
            ->where('users.delete_status',0)->where('users.status','ACTIVE') 
            ->sum('student_class_mappings.paid_fees'); 

        if($total_collected > 0) { } else { $total_collected = 0; }

        return CommonController::price_format($total_collected);
    }

    public function getTotalConcessionAttribute() {
        $total_concession = 0;

        if(self::$acadamic_year) {
            $acadamic_year = self::$acadamic_year;
        } else {
            $settings = DB::table('admin_settings')->orderby('id', 'asc')->first();
            $acadamic_year = $settings->acadamic_year;
        }

        $total_concession = DB::table('student_class_mappings')->leftjoin('users', 'users.id', 'student_class_mappings.user_id')   
            ->where('student_class_mappings.academic_year', $acadamic_year)
            ->where('student_class_mappings.class_id', $this->class_id)
            ->where('student_class_mappings.section_id', $this->id) 
            ->where('users.delete_status',0)->where('users.status','ACTIVE') 
            ->sum('student_class_mappings.concession_fees'); 

        if($total_concession > 0) { } else { $total_concession = 0; }

        return CommonController::price_format($total_concession);
    }

    public function getTotalBalanceAttribute() {
        $total_balance = 0;

        if(self::$acadamic_year) {
            $acadamic_year = self::$acadamic_year;
        } else {
            $settings = DB::table('admin_settings')->orderby('id', 'asc')->first();
            $acadamic_year = $settings->acadamic_year;
        }

        $total_balance = DB::table('student_class_mappings')->leftjoin('users', 'users.id', 'student_class_mappings.user_id')   
            ->where('student_class_mappings.academic_year', $acadamic_year)
            ->where('student_class_mappings.class_id', $this->class_id)
            ->where('student_class_mappings.section_id', $this->id) 
            ->where('users.delete_status',0)->where('users.status','ACTIVE') 
            ->sum('student_class_mappings.balance_fees'); 

        if($total_balance > 0) { } else { $total_balance = 0; }

        return CommonController::price_format($total_balance);
    }

    public function getPaidPercentageAttribute() {
        $paid_fees = 0;  $total_fees = 0; $paid_percentage = 0;

        if(self::$acadamic_year) {
            $acadamic_year = self::$acadamic_year;
        } else {
            $settings = DB::table('admin_settings')->orderby('id', 'asc')->first();
            $acadamic_year = $settings->acadamic_year;
        }

        $paid_fees = DB::table('student_class_mappings')->leftjoin('users', 'users.id', 'student_class_mappings.user_id')   
            ->where('student_class_mappings.academic_year', $acadamic_year)
            ->where('student_class_mappings.class_id', $this->class_id)
            ->where('student_class_mappings.section_id', $this->id) 
            ->where('users.delete_status',0)->where('users.status','ACTIVE') 
            ->sum('student_class_mappings.paid_fees'); 

        $total_fees = DB::table('student_class_mappings')->leftjoin('users', 'users.id', 'student_class_mappings.user_id')   
            ->where('student_class_mappings.academic_year', $acadamic_year)
            ->where('student_class_mappings.class_id', $this->class_id)
            ->where('student_class_mappings.section_id', $this->id) 
            ->where('users.delete_status',0)->where('users.status','ACTIVE') 
            ->sum('student_class_mappings.total_fees'); 

        if($total_fees > 0 && $paid_fees > 0) {
            $paid_percentage = (100 * $paid_fees) / $total_fees;
        }

        if($paid_percentage > 0) { } else { $paid_percentage = 0; }

        return number_format($paid_percentage,2);
    }

}