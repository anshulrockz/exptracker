<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Report;
use App\AssetNew;
use App\Expense;
use App\Deposit;
use App\Transaction;

class ReportController extends Controller
{
    public function deposit()
    {
    	$deposit = Report::all_deposits();
        return view('report.deposit')->with('report', $deposit);
    }
	
	public function expense()
    {
    	$expense = Report::all_expenses(); 
        return view('report.expense')->with('report', $expense);
    }

	public function asset()
    {
        $asset = Report::all_assets();
        $assetnew = Report::all_asset_news();
        return view('report.asset')->with(array('report' => $asset, 'reportNew' => $assetnew ));
    }
    
    public function expiry()
    {
        $expiry = Report::all_assets_expiry();
        $expiry2 = Report::all_asset_news_expiry();
        return view('report.asset-expiry')->with(array('report' => $expiry, 'report2' => $expiry2 ));
    }
    
	public function ledger()
    {
        $transaction = Report::all_transaction();
        return view('report.ledger')->with('report', $transaction);
    }

	public function overall()
    {
        $tests = Report::all();
        return view('report.overall')->with('report', $expense);
    }
    
    public function datatable_ajax(Request $request)
    {
        // $table_name = $request->table_name;
        // $start_date = $request->start_date;
        // $end_date = $request->end_date; die;
        // $data = Expense::->whereBetween('created_at', array($from, $to))->get();
        // print_r(json_encode($table_name));
    }
    
}
