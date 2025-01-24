<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB; 
class Module extends Model
{
    public $fillable = ['module_name','parent_module_fk'];

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
    protected $table = 'modules';
    
    protected $appends = [
        'is_parent_module',
        'is_status'
        
    ];
    public function childs() {
        
        return $this->hasMany('App\Models\Module','parent_module_fk','id') ;
        
    }
    public function getIsParentModuleAttribute()
    {
        $user=Module::find($this->parent_module_fk);
        if(!empty($user)){
            return $user->module_name;
        }
        
    }
    public function getIsStatusAttribute()
    {
        $email_ver = $this->status;
        if ($email_ver == 1) {
            $email_ver = "Active";
        } else if ($email_ver == 2) {
            $email_ver = "Inactive";
        }
        return $email_ver;
    }
   
}
