<?php

namespace App;

use DB;
use Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentVendor extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];


    public function UserDetails()
    {
        return $this->hasOne('App\User', 'id','created_for');
    }
    
    public static function all_deposits()
	{
		$company = Auth::user()->company_id;
		$workshop = Auth::user()->workshop_id;
		$id = Auth::user()->id;
		$user_type = Auth::user()->user_type;
		
		if($user_type == 1  || $user_type == 5){
			return DB::table('payment_vendors')
			->select('payment_vendors.*', 'users.name as user')
			->where([
			['payment_vendors.deleted_at',null],
					    //['users.workshop_id', $workshop],
					    //['payment_vendors.created_for', $id],
					    //['users.id', $id]
						])
	            ->leftJoin('users', 'users.id', '=', 'payment_vendors.created_for')
				->get();
		}

       	if($user_type == 3){
			return DB::table('payment_vendors')
				->select('payment_vendors.*', 'users.name as user')
				->where([
				['payment_vendors.deleted_at',null],
				['payment_vendors.created_by', $id],
				['users.workshop_id', $workshop],
				])
	            ->leftJoin('users', 'users.id', '=', 'payment_vendors.created_for')
	            ->get();
		}

		else{
			return DB::table('user_payment_vendors')
				->select('user_payment_vendors.*')//,  DB::raw('SUM( user_payment_vendors.amount - (expense_details.cost*expense_details.quantity) - expense_details.sgst - expense_details.cgst - expense_details.igst ) as rem_amount'))
				->where([
						['user_payment_vendors.deleted_at',null],
						['user_payment_vendors.created_by', $id],
						])
	            // ->leftJoin('expenses', 'expenses.created_for', '=', 'user_payment_vendors.created_for')
	            // ->leftJoin('expense_details', 'expense_details.expense_id', '=', 'expenses.id')
	            ->get();
		}
	}
	
	public static function workshop_payment_vendors()
	{
		$id = Auth::user()->id;
		$company = Auth::user()->company_id;
		$workshop = Auth::user()->workshop_id;
		
		return DB::table('payment_vendors')
			->select('payment_vendors.*', 'users.name as user')
			->where([
			['users.company_id',$company],
			['users.workshop_id',$workshop],
			['payment_vendors.created_by',$id],
			['payment_vendors.deleted_at',null]
			])
            ->leftJoin('users', 'users.id', '=', 'payment_vendors.created_for')
            ->get();
	}

	public static function deposit_bar_chart()
	{
		$company = Auth::user()->company_id;
		$workshop = Auth::user()->workshop_id;
		$id = Auth::user()->id;
		$user_type = Auth::user()->user_type;
		
		if(isset($_GET['location']))
		{
			$company = Auth::user()->company_id;
			$workshop = $_GET['location'];
			$user_type = 3;
		}

		if($user_type == 1  || $user_type == 5){
			return DB::table('payment_vendors')
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
			return DB::table('payment_vendors')
				->select( DB::raw('YEAR(date) AS y'), DB::raw('MONTH(date) AS m'), DB::raw('SUM(amount) as total') )
				->where( [
							[DB::raw('YEAR(date)'), "=",date('Y')],
					    ['users.workshop_id', $workshop],
							['payment_vendors.deleted_at', null]
						])
				->groupBy('y', 'm')
					->orderBy('m', 'asc')
	            ->leftJoin('users', 'users.id', '=', 'payment_vendors.created_for')->get();
		}

		if($user_type == 4){
			return DB::table('payment_vendors')
				->select( DB::raw('YEAR(payment_vendors.date) AS y'), DB::raw('MONTH(payment_vendors.date) AS m'), DB::raw('SUM(payment_vendors.amount) as total') )
				->where( [
						[DB::raw('YEAR(payment_vendors.date)'), "=",date('Y')],
					    ['users.workshop_id', $workshop],
					    ['payment_vendors.created_for', $id],
						['payment_vendors.deleted_at', null]
						])
				->groupBy('y', 'm')
					->orderBy('m', 'asc')
	            ->leftJoin('users', 'users.id', '=', 'payment_vendors.created_for')->get();

			// return DB::table('payment_vendors')
			// 	->select( DB::raw('YEAR(payment_vendors.created_at) AS y'), DB::raw('MONTH(payment_vendors.created_at) AS m'), DB::raw('SUM(payment_vendors.amount) as total') )
			// 	->where([
			// 		    ['payment_vendors.deleted_at', null],
			// 		    ['users.workshop_id', $workshop],
			// 		    ['payment_vendors.created_for', $id],
			// 		    ['users.id', $id]
			// 			])
	  //           ->leftJoin('users', 'users.id', '=', 'payment_vendors.created_for')
			// 	->groupBy('y', 'm')
			// 		->orderBy('m', 'asc')
			// 	->get();
		}
	}

	public static function deposit_table()
	{
		return DB::table('payment_vendors')
				->select( "payment_vendors.*")
				->where([
				[ DB::raw('YEAR(date)'), "=","2018"],
				["amount","=>","10"]
				]
				)
				->groupBy('y', 'm')
				->get();
	}
	
	public static function payeeBalance($nameofpayee)
	{
		$payment_vendors = DB::table('user_payment_vendors')
			->select(DB::raw('sum(amount) as amt'))
			->where([
			['user_payment_vendors.created_for',$nameofpayee],
			['user_payment_vendors.deleted_at',null]
			])
            ->first();

      	$user_returns = DB::table('user_returns')
			->select(DB::raw('sum(amount) as amt'))
			->where([
			['user_returns.by_user',$nameofpayee],
			['user_returns.deleted_at',null]
			])
            ->first();
        
        $expenses = DB::table('expenses')
			->select(DB::raw('SUM(expense_details.cost*expense_details.quantity) as cost'), DB::raw('SUM(expense_details.quantity) as quantity'), DB::raw('SUM(expense_details.sgst) as sgst'), DB::raw('SUM(expense_details.cgst) as cgst'), DB::raw('SUM(expense_details.igst) as igst') )
			->where([
					['expenses.created_for',$nameofpayee],
					['expenses.status',1],
					['expenses.deleted_at',null],
					['expense_details.deleted_at',null]
					])
            ->leftJoin('expense_details', 'expense_details.expense_id', '=', 'expenses.id')
            ->first();

    	return $payment_vendors->amt - $user_returns->amt - $expenses->cost - $expenses->sgst - $expenses->cgst - $expenses->igst; 
         
	}
}
