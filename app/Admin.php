<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Admin extends Authenticatable
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    
    use Notifiable;
	protected $guard = 'admin';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'job_title',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    
    
    
    
    
    public function EmployeeType()
    {
        return $this->hasOne('App\EmployeeType', 'id','user_type');
    }
    
    public function Workshop()
    {
        return $this->hasOne('App\Workshop', 'id','workshop_id');
    }
    
    public function Designation()
    {
        return $this->hasOne('App\Designation', 'id','designation');
    }
   
    public function Department()
    {
        return $this->hasOne('App\Department', 'id','department_id');
    }
}
