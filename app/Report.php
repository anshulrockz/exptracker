<?php

namespace App;

use DB;
use Carbon\Carbon;
use Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Report extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    
    public static function all_deposits()
	{
		$company = Auth::user()->company_id;
		$workshop = Auth::user()->workshop_id;
		$user_id = Auth::user()->id;
		
		if(Auth::user()->user_type == 1 || Auth::user()->user_type==5)
		{
			return DB::table('deposits')
				->select('deposits.*', 'users.name as user', 'workshops.name as location')
				->where([
				//['users.company_id',$company],
				['deposits.deleted_at',null]
				])
	            ->leftJoin('users', 'users.id', '=', 'deposits.to_user')
	            ->leftJoin('workshops', 'users.workshop_id', '=', 'workshops.id')
	            ->get();
		}
		
		if(Auth::user()->user_type == 3)
		{
			return DB::table('deposits')
				->select('deposits.*', 'users.name as user', 'workshops.name as location')
				->where([
				['users.company_id',$company],
				['users.workshop_id',$workshop],
				['users.user_type', '!=', '1'],
				['deposits.deleted_at',null]
				])
	            ->leftJoin('users', 'users.id', '=', 'deposits.to_user')
	            ->leftJoin('workshops', 'users.workshop_id', '=', 'workshops.id')
	            ->get();
		}
		
		if(Auth::user()->user_type == 4)
		{
			return DB::table('deposits')
				->select('deposits.*', 'users.name as user', 'workshops.name as location')
				->where([
				['deposits.to_user',$user_id],
				['deposits.deleted_at',null]
				])
	            ->leftJoin('users', 'users.id', '=', 'deposits.to_user')
	            ->leftJoin('workshops', 'users.workshop_id', '=', 'workshops.id')
	            ->get();
		}
	}
	
	public static function all_assets()
	{
		$company = Auth::user()->company_id;
		$workshop = Auth::user()->workshop_id;
		$user_id = Auth::user()->id;
		
		if(Auth::user()->user_type == 1 || Auth::user()->user_type==5)
		{
			return DB::table('assets')
				->select('assets.*', 'users.name as user', 'workshops.name as location','vehicles.registration as reg', 'vehicles.insurance as insurance', 'vehicles.puc as puc')
				->where([
				['users.company_id',$company],
				['assets.deleted_at',null]
				])
	            ->leftJoin('users', 'users.id', '=', 'assets.created_by')
	            ->leftJoin('workshops', 'assets.location', '=', 'workshops.id')
	            ->leftJoin('vehicles', 'vehicles.asset_id', '=', 'assets.id')
	            ->groupBy('assets.id')
	            ->get();
		}
		
		if(Auth::user()->user_type == 3)
		{
			return DB::table('assets')
				->select('assets.*', 'users.name as user', 'workshops.name as location', 'vehicles.registration as reg', 'vehicles.insurance as insurance', 'vehicles.puc as puc')
				->where([
				['users.company_id',$company],
				['users.workshop_id',$workshop],
				//['users.user_type', '!=', '1'],
				['assets.deleted_at',null]
				])
	            ->leftJoin('users', 'users.id', '=', 'assets.created_by')
	            ->leftJoin('workshops', 'assets.location', '=', 'workshops.id')
	            ->leftJoin('vehicles', 'vehicles.asset_id', '=', 'assets.id')
	            ->groupBy('assets.id')
	            ->get();
		}
		
		if(Auth::user()->user_type == 4)
		{
			return DB::table('assets')
				->select('assets.*', 'users.name as user', 'workshops.name as location')
				->where([
				['users.company_id',$company],
				['users.workshop_id',$workshop],
				['users.id',$user_id],
				['assets.deleted_at',null]
				])
	            ->leftJoin('users', 'users.id', '=', 'assets.created_by')
	            ->leftJoin('workshops', 'assets.location', '=', 'workshops.id')
	            ->get();
		}
	}

	public static function all_asset_news()
	{
		$company = Auth::user()->company_id;
		$workshop = Auth::user()->workshop_id;
		$user_id = Auth::user()->id;
		
		if(Auth::user()->user_type == 1 || Auth::user()->user_type==5)
		{
			return DB::table('asset_news')
				->select('asset_news.*', 'users.name as user', 'workshops.name as location','vehicle_news.registration as reg', 'vehicle_news.insurance as insurance', 'vehicle_news.puc as puc')
				->where([
				['users.company_id',$company],
				['asset_news.deleted_at',null]
				])
	            ->leftJoin('users', 'users.id', '=', 'asset_news.created_by')
	            ->leftJoin('workshops', 'asset_news.location', '=', 'workshops.id')
	            ->leftJoin('vehicle_news', 'vehicle_news.asset_id', '=', 'asset_news.id')
	            ->groupBy('asset_news.id')
	            ->get();
		}
		
		if(Auth::user()->user_type == 3)
		{
			return DB::table('asset_news')
				->select('asset_news.*', 'users.name as user', 'workshops.name as location','vehicle_news.registration as reg', 'vehicle_news.insurance as insurance', 'vehicle_news.puc as puc')
				->where([
				//['users.company_id',$company],
				['users.workshop_id',$workshop],
				['asset_news.deleted_at',null]
				])
	            ->leftJoin('users', 'users.id', '=', 'asset_news.created_by')
	            ->leftJoin('workshops', 'asset_news.location', '=', 'workshops.id')
	            ->leftJoin('vehicle_news', 'vehicle_news.asset_id', '=', 'asset_news.id')
	            ->groupBy('asset_news.id')
	            ->get();
		}
		
		if(Auth::user()->user_type == 4)
		{
			return DB::table('asset_news')
				->select('asset_news.*', 'users.name as user', 'workshops.name as location')
				->where([
				['users.company_id',$company],
				['users.workshop_id',$workshop],
				['users.id',$user_id],
				['asset_news.deleted_at',null]
				])
	            ->leftJoin('users', 'users.id', '=', 'asset_news.created_by')
	            ->leftJoin('workshops', 'asset_news.location', '=', 'workshops.id')
	            ->get();
		}
	}
	
	public static function all_assets_expiry()
	{
		$company = Auth::user()->company_id;
		$workshop = Auth::user()->workshop_id;
		$user_id = Auth::user()->id;
		//$startDate = Carbon::now()->format('Y-m-d');
		$endDate = Carbon::now()->addMonths(1)->format('Y-m-d');
		
		if(Auth::user()->user_type == 1 || Auth::user()->user_type==5)
		{
			return DB::table('assets')
				->select('assets.*', 'users.name as user')
				->where([
				['users.company_id',$company],
				['assets.deleted_at',null],
				['assets.expiry','<=',$endDate],
				])
        		//->whereBetween('assets.expiry', [$startDate, $endDate])
	            ->leftJoin('users', 'users.id', '=', 'assets.created_by')
	            ->get();
		}
		
		if(Auth::user()->user_type == 3)
		{
			return DB::table('assets')
				->select('assets.*', 'users.name as user')
				->where([
				['users.company_id',$company],
				['users.workshop_id',$workshop],
				['assets.expiry','<=',$endDate],
				['assets.deleted_at',null]
				])
        		//->whereBetween('assets.expiry', [$startDate, $endDate])
	            ->leftJoin('users', 'users.id', '=', 'assets.created_by')
	            ->get();
		}
		
		if(Auth::user()->user_type == 4)
		{
			return DB::table('assets')
				->select('assets.*', 'users.name as user')
				->where([
				['users.company_id',$company],
				['users.workshop_id',$workshop],
				['users.id',$user_id],
				['assets.expiry','<=',$endDate],
				['assets.deleted_at',null]
				])
        		//->whereBetween('assets.expiry', [$startDate, $endDate])
        		->orderBy('assets.expiry')
	            ->leftJoin('users', 'users.id', '=', 'assets.created_by')
	            ->get();
		}
	}
	
	public static function all_asset_news_expiry()
	{
		$company = Auth::user()->company_id;
		$workshop = Auth::user()->workshop_id;
		$user_id = Auth::user()->id;
		//$startDate = Carbon::now()->format('Y-m-d');
		$endDate = Carbon::now()->addMonths(1)->format('Y-m-d');
		
		if(Auth::user()->user_type == 1 || Auth::user()->user_type==5)
		{
			return DB::table('asset_news')
				->select('asset_news.*', 'users.name as user')
				->where([
				['users.company_id',$company],
				['asset_news.deleted_at',null],
				['asset_news.expiry','<=',$endDate],
				])
        		//->whereBetween('asset_news.expiry', [$startDate, $endDate])
	            ->leftJoin('users', 'users.id', '=', 'asset_news.created_by')
	            ->get();
		}
		
		if(Auth::user()->user_type == 3)
		{
			return DB::table('asset_news')
				->select('asset_news.*', 'users.name as user')
				->where([
				['users.company_id',$company],
				['users.workshop_id',$workshop],
				['asset_news.expiry','<=',$endDate],
				['asset_news.deleted_at',null]
				])
        		//->whereBetween('asset_news.expiry', [$startDate, $endDate])
	            ->leftJoin('users', 'users.id', '=', 'asset_news.created_by')
	            ->get();
		}
		
		if(Auth::user()->user_type == 4)
		{
			return DB::table('asset_news')
				->select('asset_news.*', 'users.name as user')
				->where([
				['users.company_id',$company],
				['users.workshop_id',$workshop],
				['users.id',$user_id],
				['asset_news.expiry','<=',$endDate],
				['asset_news.deleted_at',null]
				])
        		//->whereBetween('asset_news.expiry', [$startDate, $endDate])
        		->orderBy('asset_news.expiry')
	            ->leftJoin('users', 'users.id', '=', 'asset_news.created_by')
	            ->get();
		}
	}
	
	public static function all_expenses()
	{
		$company = Auth::user()->company_id;
		$workshop = Auth::user()->workshop_id;
		$user_id = Auth::user()->id;
		
		if(Auth::user()->user_type == 1 || Auth::user()->user_type==5)
		{
			return DB::table('expenses')
				->select('expenses.*', 'expense_details.*', 'users.name as user', 'workshops.name as location')
				->where([
				//['users.company_id',$company],
				//['expenses.status',1],
				['expenses.deleted_at',null],
				['expense_details.deleted_at',null],
				])
	            ->leftJoin('users', 'users.id', '=', 'expenses.created_by')
	            ->leftJoin('expense_details', 'expense_details.expense_id', '=', 'expenses.id')
	            ->leftJoin('workshops', 'users.workshop_id', '=', 'workshops.id')
	            ->get();
		}
		
		if(Auth::user()->user_type == 3)
		{
			return DB::table('expenses')
				->select('expenses.*', 'expense_details.*', 'users.name as user', 'workshops.name as location')
				->where([
				['users.company_id',$company],
				['users.workshop_id',$workshop],
				//['expenses.status',1],
				['expenses.deleted_at',null],
				['expense_details.deleted_at',null],
				])
	            ->leftJoin('users', 'users.id', '=', 'expenses.created_by')
	            ->leftJoin('expense_details', 'expense_details.expense_id', '=', 'expenses.id')
	            ->leftJoin('workshops', 'users.workshop_id', '=', 'workshops.id')
	            ->get();
		}
		
		if(Auth::user()->user_type == 4)
		{
			return DB::table('expenses')
				->select('expenses.*', 'expense_details.*', 'users.name as user', 'workshops.name as location')
				->where([
				['users.company_id',$company],
				['users.workshop_id',$workshop],
				['users.id',$user_id],
				['expense_details.created_by',$user_id],
				['expenses.created_by',$user_id],
				//['expenses.status',1],
				['expenses.deleted_at',null],
				['expense_details.deleted_at',null],
				])
	            ->leftJoin('users', 'users.id', '=', 'expenses.created_by')
	            ->leftJoin('expense_details', 'expense_details.expense_id', '=', 'expenses.id')
	            ->leftJoin('workshops', 'users.workshop_id', '=', 'workshops.id')
	            ->get();
		}
	}
	
	public static function all_transaction()
	{
		$company = Auth::user()->company_id;
		$workshop = Auth::user()->workshop_id;
		$user_id = Auth::user()->id;
		
		if(Auth::user()->user_type == 1)
		{
			return DB::table('transactions')
				->select('transactions.*', 'users.name as user')
				->where([
				['users.company_id',$company],
				['transactions.deleted_at',null]
				])
	            ->leftJoin('users', 'users.id', '=', 'transactions.created_by')
	            //->leftJoin('users', 'users.id', '=', 'transactions.created_by')
	            ->get();
		}
		
		if(Auth::user()->user_type == 2)
		{
			return DB::table('transactions')
				->select('transactions.*', 'users.name as user')
				->where([
				['users.company_id',$company],
				['users.workshop_id',$workshop],
				['transactions.deleted_at',null]
				])
	            ->leftJoin('users', 'users.id', '=', 'transactions.created_by')
	            ->get();
		}
		
		if(Auth::user()->user_type == 3)
		{
			return DB::table('transactions')
				->select('transactions.*', 'users.name as user')
				->where([
				['users.company_id',$company],
				['users.workshop_id',$workshop],
				['users.id',$user_id],
				['transactions.deleted_at',null]
				])
	            ->leftJoin('users', 'users.id', '=', 'transactions.created_by')
	            ->get();
		}
	}

	
}
