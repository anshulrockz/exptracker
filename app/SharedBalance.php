<?php

namespace App;

use DB;
use Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SharedBalance extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];


    public function UserDetails()
    {
        return $this->hasOne('App\User', 'id','to_user');
    }
    
    public static function all_shared_balances()
	{
		$company = Auth::user()->company_id;
		$workshop = Auth::user()->workshop_id;
		$id = Auth::user()->id;
		$user_type = Auth::user()->user_type;
		
		if($user_type == 1  || $user_type == 5){
			return DB::table('shared_balances')
			->select('shared_balances.*', 'users.name as user')
			->where([
			['shared_balances.deleted_at',null],
					    //['users.workshop_id', $workshop],
					    //['shared_balances.to_user', $id],
					    //['users.id', $id]
						])
	            ->leftJoin('users', 'users.id', '=', 'shared_balances.to_user')
				->get();
		}

        	/*if($user_type == 3){
			return DB::table('shared_balances')
			->select('shared_balances.*', 'users.name as user')
			->where([
					['shared_balances.deleted_at',null],
					['users.workshop_id', $workshop],
					//['shared_balances.to_user', $id],
					//['users.id', $id]
						])
	            ->leftJoin('users', 'users.id', '=', 'shared_balances.to_user')
				->get();
		}*/

		else{
			return DB::table('shared_balances')
				->select('shared_balances.*', 'users.name as user')
				->where([
				['shared_balances.deleted_at',null],
					//['shared_balances.to_user', $id],
					['users.workshop_id', $workshop],
				])
	            ->leftJoin('users', 'users.id', '=', 'shared_balances.to_user')
	            ->get();
		}
	}
	
	public static function workshop_shared_balances()
	{
		$id = Auth::user()->id;
		$company = Auth::user()->company_id;
		$workshop = Auth::user()->workshop_id;
		
		return DB::table('shared_balances')
			->select('shared_balances.*', 'users.name as user')
			->where([
			['users.company_id',$company],
			['users.workshop_id',$workshop],
			['shared_balances.created_by',$id],
			['shared_balances.deleted_at',null]
			])
            ->leftJoin('users', 'users.id', '=', 'shared_balances.to_user')
            ->get();
	}

	public static function deposit_bar_chart()
	{
		$company = Auth::user()->company_id;
		$workshop = Auth::user()->workshop_id;
		$id = Auth::user()->id;
		$user_type = Auth::user()->user_type;

		if($user_type == 1  || $user_type == 5){
			return DB::table('shared_balances')
				->select( DB::raw('YEAR(date) AS y'), DB::raw('MONTH(date) AS m'), DB::raw('SUM(amount) as total') )
				->where( [
							[DB::raw('YEAR(date)'), "=",date('Y')],
							['deleted_at', null]
						])
				->groupBy('y', 'm')
					->orderBy('m', 'asc')
				->get();
		}

		if($user_type == 3){
			return DB::table('shared_balances')
				->select( DB::raw('YEAR(date) AS y'), DB::raw('MONTH(date) AS m'), DB::raw('SUM(amount) as total') )
				->where( [
							[DB::raw('YEAR(date)'), "=",date('Y')],
					    ['users.workshop_id', $workshop],
							['shared_balances.deleted_at', null]
						])
				->groupBy('y', 'm')
					->orderBy('m', 'asc')
	            ->leftJoin('users', 'users.id', '=', 'shared_balances.to_user')->get();
		}

		if($user_type == 4){
			return DB::table('shared_balances')
				->select( DB::raw('YEAR(shared_balances.date) AS y'), DB::raw('MONTH(shared_balances.date) AS m'), DB::raw('SUM(shared_balances.amount) as total') )
				->where( [
						[DB::raw('YEAR(shared_balances.date)'), "=",date('Y')],
					    ['users.workshop_id', $workshop],
					    ['shared_balances.to_user', $id],
						['shared_balances.deleted_at', null]
						])
				->groupBy('y', 'm')
					->orderBy('m', 'asc')
	            ->leftJoin('users', 'users.id', '=', 'shared_balances.to_user')->get();

			// return DB::table('shared_balances')
			// 	->select( DB::raw('YEAR(shared_balances.created_at) AS y'), DB::raw('MONTH(shared_balances.created_at) AS m'), DB::raw('SUM(shared_balances.amount) as total') )
			// 	->where([
			// 		    ['shared_balances.deleted_at', null],
			// 		    ['users.workshop_id', $workshop],
			// 		    ['shared_balances.to_user', $id],
			// 		    ['users.id', $id]
			// 			])
	  //           ->leftJoin('users', 'users.id', '=', 'shared_balances.to_user')
			// 	->groupBy('y', 'm')
			// 		->orderBy('m', 'asc')
			// 	->get();
		}
	}

	public static function deposit_table()
	{
		return DB::table('shared_balances')
				->select( "shared_balances.*")
				->where([
				[ DB::raw('YEAR(date)'), "=","2018"],
				["amount","=>","10"]
				]
				)
				->groupBy('y', 'm')
				->get();
	}
}
