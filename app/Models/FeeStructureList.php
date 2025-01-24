<?php

namespace App\Models;
use DB;

use Illuminate\Database\Eloquent\Model;


class FeeStructureList extends Model
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
        protected $table = 'fee_structure_lists';

        protected $appends = [ 'is_category_name','is_fee_type', 'is_total', 'is_cancelled_by']; 

        public static $student_id; 

        public static $fee_structure_item_id;

        public static $paiditems;

      public function feeItems(){
        $ge[] = 1;
        if(isset(self::$student_id) && (self::$student_id) > 0) {
          $gender = DB::table('users')->where('id', self::$student_id)->value('gender'); 
          if(isset($gender) && !empty($gender)) {
            if(strtolower($gender) == 'male') {
              $ge[] = 2;
            } else if(strtolower($gender) == 'female') {
              $ge[] = 3;
            }
          }
        }
         
        if(isset(self::$fee_structure_item_id) && (self::$fee_structure_item_id) > 0) {
          return $this->hasMany('App\Models\FeeStructureItem','fee_structure_id','id')->where('id', self::$fee_structure_item_id)
            ->whereIn('gender', $ge)->where('cancel_status', 0)
            ->select('id','fee_term_id','fee_structure_id','fee_item_id','gender','amount','due_date');
        } else { 
          if(isset(self::$paiditems) && (is_array(self::$paiditems)) && count(self::$paiditems)  > 0) {
            return $this->hasMany('App\Models\FeeStructureItem','fee_structure_id','id')->whereIn('id', self::$paiditems)
              ->whereIn('gender', $ge)->where('cancel_status', 0)
              ->select('id','fee_term_id','fee_structure_id','fee_item_id','gender','amount','due_date');
          } else {  
            return $this->hasMany('App\Models\FeeStructureItem','fee_structure_id','id')->whereIn('gender', $ge)
              ->where('cancel_status', 0)
              ->select('id','fee_term_id','fee_structure_id','fee_item_id','gender','amount','due_date');
          }
        }
          
          

      }


      public function feeDeletedItems(){
        $ge[] = 1;
        if(isset(self::$student_id) && (self::$student_id) > 0) {
          $gender = DB::table('users')->where('id', self::$student_id)->value('gender'); 
          if(isset($gender) && !empty($gender)) {
            if(strtolower($gender) == 'male') {
              $ge[] = 2;
            } else if(strtolower($gender) == 'female') {
              $ge[] = 3;
            }
          }
        }
         
        if(isset(self::$fee_structure_item_id) && (self::$fee_structure_item_id) > 0) {
          return $this->hasMany('App\Models\FeeStructureItem','fee_structure_id','id')->where('id', self::$fee_structure_item_id)
            ->whereIn('gender', $ge)->where('cancel_status', 2)
            ->select('id','fee_term_id','fee_structure_id','fee_item_id','gender','amount','due_date', 'deleted_item_id');
        } else { 
          if(isset(self::$paiditems) && (is_array(self::$paiditems)) && count(self::$paiditems)  > 0) {
            return $this->hasMany('App\Models\FeeStructureItem','fee_structure_id','id')->whereIn('id', self::$paiditems)
              ->whereIn('gender', $ge)->where('cancel_status', 2)
              ->select('id','fee_term_id','fee_structure_id','fee_item_id','gender','amount','due_date', 'deleted_item_id');
          } else {  
            return $this->hasMany('App\Models\FeeStructureItem','fee_structure_id','id')->whereIn('gender', $ge)
              ->where('cancel_status', 2)
              ->select('id','fee_term_id','fee_structure_id','fee_item_id','gender','amount','due_date', 'deleted_item_id');
          }
        }
          
          

      }

      public function feeCancelledItems(){
        $ge[] = 1;
        if(isset(self::$student_id) && (self::$student_id) > 0) {
          $gender = DB::table('users')->where('id', self::$student_id)->value('gender'); 
          if(isset($gender) && !empty($gender)) {
            if(strtolower($gender) == 'male') {
              $ge[] = 2;
            } else if(strtolower($gender) == 'female') {
              $ge[] = 3;
            }
          }
        }
         
        if(isset(self::$fee_structure_item_id) && (self::$fee_structure_item_id) > 0) {
          return $this->hasMany('App\Models\FeeStructureItem','fee_structure_id','id')->where('id', self::$fee_structure_item_id)
            ->whereIn('gender', $ge)->whereIn('cancel_status', [1,2])
            ->select('id','fee_term_id','fee_structure_id','fee_item_id','gender','amount','due_date', 'deleted_item_id');
        } else { 
          if(isset(self::$paiditems) && (is_array(self::$paiditems)) && count(self::$paiditems)  > 0) {
            return $this->hasMany('App\Models\FeeStructureItem','fee_structure_id','id')->whereIn('id', self::$paiditems)
              ->whereIn('gender', $ge)->whereIn('cancel_status', [1,2])
              ->select('id','fee_term_id','fee_structure_id','fee_item_id','gender','amount','due_date', 'deleted_item_id');
          } else {  
            return $this->hasMany('App\Models\FeeStructureItem','fee_structure_id','id')->whereIn('gender', $ge)
              ->whereIn('cancel_status', [1,2])
              ->select('id','fee_term_id','fee_structure_id','fee_item_id','gender','amount','due_date', 'deleted_item_id');
          }
        }
          
          

      }
      
      public function getIsCategoryNameAttribute()    {
            
        return DB::table('fee_categories')->where('id', $this->fee_category_id)->where('school_id', $this->school_id)->value('name');
        
      } 

      public function getIsCancelledByAttribute()    {
        return DB::table('users')->where('id', $this->cancelled_by)->value('name');
      }

      public function getIsTotalAttribute()    {
            
        return DB::table('fee_structure_items')->where('fee_structure_id', $this->id)->where('cancel_status', 0)->sum('amount');
        
      } 
    

      public function getIsFeeTypeAttribute()    { 
      
        if($this->fee_type == 1){

          return 'Mandatory';
        }

        if($this->fee_type == 2){

          return 'Variable';
        }
        
      } 


      
  
   
}
