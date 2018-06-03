<?php

/***************************************************
 ******* Developed By:- Anshul Agrawal *************
 ******* Email:- anshul.agrawal889@gmail.com *******
 ******* Phone:- 9720044889 ************************
 ***************************************************/

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Workshop;
use App\Expense;
use App\Deposit;
use App\Asset;
use App\AssetNew;
use Auth;

class HomeController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        //try{
          $deposits_2 = Deposit::all_deposits()->where('amount','>','0')->take(10);
          if(Auth::user()->user_type == 4)
          {
          	$deposits_2 = Deposit::all_deposits()->where('to_user',Auth::user()->id)->take(10);
          }
          $expenses = Expense::expense_bar_chart(); 
          $expense_pc = Expense::expense_pie_chart(); 
          $deposits = Deposit::deposit_bar_chart(); 
          $asset_old = Asset::assetold_pie_chart();
          $asset_new = AssetNew::assetnew_pie_chart();

          $last_month_d = $last_month_e  = $total_e = $total_a = $total1 = $total2 =0;
      
          
          foreach($expense_pc as $key => $value)
            $total_e += $value->cost+$value->sgst+$value->cgst+$value->igst;
                       
          foreach($asset_old as $key => $value)
            $total1 += $value->total;
            
            foreach($asset_new as $key => $value)
            $total2 += $value->total;      

            $total_a = $total1+$total2;
            
            if(count($deposits)>1){
              $count_month_d = count($deposits)-1;
              $last_month_d = $deposits[$count_month_d]->m+1;
            }
            
            if(count($expenses)>1){
              $count_month_e = count($expenses)-1;
              $last_month_e = $expenses[$count_month_e]->m+1;
            }
            
          $workshops = Workshop::all();

          return view('home')->with(array('workshops' => $workshops, 'expenses' => $expenses, 'deposits' => $deposits, 'asset_old' => $asset_old, 'asset_new' => $asset_new, 'expense_pc' => $expense_pc, 'deposits_2' => $deposits_2,'total_e' => $total_e, 'total_a' => $total_a, 'last_month' => $last_month_d, 'last_month_e' => $last_month_e ));
        // }
        // catch(\Exception $e){
        //   $error = $e->getMessage();
        //   return back()->with('error', 'Something went wrong! Please contact admin');
        // }
    }
}
