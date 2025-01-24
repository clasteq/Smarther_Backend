<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class StudentAcademics extends Model
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
    protected $table = 'student_class_mappings';

    protected $appends = ['display_academic_year'];

    public function getDisplayAcademicYearAttribute() {
        $academic_year = $this->academic_year;

        if($academic_year > 0) { 
            $plus = $academic_year + 1;
            $display_academic_year = $academic_year .' - '. $plus;
        }   else { 
            $display_academic_year = $academic_year;
        }
        return $display_academic_year;
    }
    
}
