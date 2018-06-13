<?php

namespace App;

use DB;
use Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentNeft extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    public static function lastid()
	{
		return DB::table('payment_nefts')->orderBy('id', 'desc')->first();
	}

    public function UserDetails()
    {
        return $this->hasOne('App\User', 'id','created_by');
    }
    
    public static function all_payment_nefts()
	{
		$company = Auth::user()->company_id;
		$workshop = Auth::user()->workshop_id;
		$id = Auth::user()->id;
		$user_type = Auth::user()->user_type;
		
		if($user_type == 1  || $user_type == 5){
			return DB::table('payment_nefts')
			->select('payment_nefts.*', 'users.name as user')
			->where([
			['payment_nefts.deleted_at',null],
					    //['users.workshop_id', $workshop],
					    //['payment_nefts.created_by', $id],
					    //['users.id', $id]
						])
	            ->leftJoin('users', 'users.id', '=', 'payment_nefts.created_by')
				->get();
		}

       	if($user_type == 3){
			return DB::table('payment_nefts')
				->select('payment_nefts.*', 'users.name as user')
				->where([
				['payment_nefts.deleted_at',null],
				['payment_nefts.created_by', $id],
				['users.workshop_id', $workshop],
				])
	            ->leftJoin('users', 'users.id', '=', 'payment_nefts.created_by')
	            ->get();
		}

		else{
			return DB::table('payment_nefts')
				->select('payment_nefts.*')
				->where([
						['payment_nefts.deleted_at',null],
						['payment_nefts.created_by', $id],
						])
	            ->get();
		}
	}
	
}
