<?php

namespace App;

use DB;
use Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cheque extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    public static function lastid()
	{
		return DB::table('assets')->orderBy('id', 'desc')->first();
	}

    public function UserDetails()
    {
        return $this->hasOne('App\User', 'id','to_user');
    }
    
    public static function all_cheques()
	{
		$company = Auth::user()->company_id;
		$workshop = Auth::user()->workshop_id;
		$id = Auth::user()->id;
		$user_type = Auth::user()->user_type;
		
		if($user_type == 1  || $user_type == 5){
			return DB::table('cheques')
			->select('cheques.*', 'users.name as user')
			->where([
			['cheques.deleted_at',null],
					    //['users.workshop_id', $workshop],
					    //['cheques.to_user', $id],
					    //['users.id', $id]
						])
	            ->leftJoin('users', 'users.id', '=', 'cheques.to_user')
				->get();
		}

       	if($user_type == 3){
			return DB::table('cheques')
				->select('cheques.*', 'users.name as user')
				->where([
				['cheques.deleted_at',null],
				['cheques.created_by', $id],
				['users.workshop_id', $workshop],
				])
	            ->leftJoin('users', 'users.id', '=', 'cheques.to_user')
	            ->get();
		}

		else{
			return DB::table('cheques')
				->select('cheques.*')
				->where([
						['cheques.deleted_at',null],
						['cheques.created_by', $id],
						])
	            ->get();
		}
	}
	
}
