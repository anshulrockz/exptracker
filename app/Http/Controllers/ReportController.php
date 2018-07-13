<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Report;
use App\AssetNew;
use App\Expense;
use App\Deposit;
use App\PaymentOther;
use App\Transaction;

class ReportController extends Controller
{
    public function deposit()
    {
        try{
        	$deposit = Report::all_deposits();
            return view('report.deposit')->with('report', $deposit);
        }
        catch(\Exception $e){
            $error = $e->getMessage();
            return back()->with('error', 'Something went wrong! Please contact admin');
        }
    }
	
	public function expense()
    {
        try{
            $expense = Report::all_expenses(); 
            return view('report.expense')->with('report', $expense);
        }
        catch(\Exception $e){
            $error = $e->getMessage();
            return back()->with('error', 'Something went wrong! Please contact admin');
        }
    }
    
    public function cheque()
    {
    	$cheque = Report::all_cheques(); 
        return view('report.cheque')->with('report', $cheque);
    }

	public function asset()
    {
        $asset = Report::all_assets();
        $assetnew = Report::all_asset_news();
        return view('report.asset')->with(array('report' => $asset, 'reportNew' => $assetnew ));
    }
    
    public function expiry()
    {
        try{
            $expiry = Report::all_assets_expiry();
            $expiry2 = Report::all_asset_news_expiry();
            return view('report.asset-expiry')->with(array('report' => $expiry, 'report2' => $expiry2 ));
        }
        catch(\Exception $e){
            $error = $e->getMessage();
            return back()->with('error', 'Something went wrong! Please contact admin');
        }
    }
    
	public function received_payment()
    {
        $transaction = Report::all_received_payment();
        return view('report.received-payment')->with('report', $transaction);
    }

	public function overall()
    {
        try{
            $tests = Report::all();
            return view('report.overall')->with('report', $expense);
        }
        catch(\Exception $e){
            $error = $e->getMessage();
            return back()->with('error', 'Something went wrong! Please contact admin');
        }
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
