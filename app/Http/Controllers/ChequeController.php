<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Cheque;
use App\Bank;
use App\Workshop;
use Auth;

class ChequeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index() 
    { 
    	$cheque = Cheque::all_cheques();
	    return view('cheque.index')->with('cheque',$cheque);
    }

    public function create()
    {
		$bank = Bank::all();
		$location = Workshop::all();
		$voucher_no = Cheque::lastid();
    	if(empty($voucher_no->id)) $voucher_no = 0;
    	else $voucher_no = $voucher_no->id;
    	$voucher_no = $voucher_no + 1;
    	$voucher_no = 'PLS_CHQ_'.$voucher_no;

		return view('cheque.create')->with(array('bank' => $bank, 'location' => $location, 'voucher_no' => $voucher_no ));
	}

    public function store(Request $request)
    {
    	$this->validate($request,[
			'date'=>'required|max:255',
			'amount'=>'required|numeric',
			'cheque_no'=>'required|max:255',
		]);
		
		$cheque = new Deposit;
		$date = $request->date;
		$cheque->date = date_format(date_create($date),"Y-m-d");
		$cheque->to_user = $request->name;
		$cheque->amount = $request->amount;
		$cheque->mode = $request->mode;
		$cheque->remark = $request->remarks;
		$cheque->user_sys = \Request::ip();
		$cheque->updated_by = Auth::id();
		$cheque->created_by = Auth::id();
		$result = $cheque->save();
		
		$id = $cheque->id;

		if(Auth::user()->user_type==4)
    	{
			$cheque = UserCheque::find($id);
		}
		else
			$cheque = Cheque::find($id);

		$cheque->txn_id = 'DPO'.$id;
		$result = $cheque->save();
		
		//trans table
		if(Auth::user()->user_type==4){
			$transaction = new UserTransaction;
			$transaction->txn_type = 1;
			$transaction->voucher_no = $cheque->txn_id;
			$transaction->credit = $request->amount;
			$transaction->balance = Cheque::payeeBalance($request->name);
			$transaction->created_for = $request->name;
			$transaction->user_sys = \Request::ip();
			$transaction->updated_by = Auth::id();
			$transaction->created_by = Auth::id();
			$transaction->particulars = Auth::user()->name.' Deposit to '.$request->name;
			$result2 = $transaction->save();
		}
		
		if($result){
			return back()->with('success', 'Record added successfully!');
		}
		else{
			return back()->with('error', 'Something went wrong!');
		}
    }

    public function show($id)
    {
        try{
        	$cheque = Cheque::find($id);
	        $userdetails = Cheque::find($id)->UserDetails;
	        return view('cheque.show')->with(array('deposit' => $cheque, 'userdetails' => $userdetails));
    	}
    	catch(\Exception $e){
			$error = $e->getMessage();
		    return back()->with('error', 'Something went wrong! Please contact admin');
		}
    }

    public function edit($id)
    {
    	try{
    		if(Auth::user()->user_type==4)
	    	{
	    		$cheque = UserCheque::find($id);
	    	}
	    	else
	    	{
		    	$cheque = Cheque::find($id);
		    	$userdetails = Cheque::find($id)->UserDetails;
		    }

	    	if(Auth::user()->user_type==1 || Auth::user()->user_type==5)
	    	{
	    		$companies = Company::all();
	    		$workshops = Workshop::all()->where('company',Auth::user()->company_id);
	        	return view('cheque.edit')->with(array('userdetails' => $userdetails, 'companies' => $companies, 'workshops' => $workshops, 'deposit' => $cheque,));
			}
			
	    	if(Auth::user()->user_type==3)
	    	{
	    		$workshops = Workshop::all()->where('company',Auth::user()->company_id);
	    		return view('cheque.edit')->with(array('workshops' => $workshops, 'deposit' => $cheque,'userdetails' => $userdetails));
			}

			if(Auth::user()->user_type==4)
	    	{
	    		
	    		return view('cheque.edit')->with(array( 'deposit' => $cheque));
			}

	        return view('cheque.edit')->with('deposit', $cheque);
    	}
    	catch(\Exception $e){
			$error = $e->getMessage();
		    return back()->with('error', 'Something went wrong! Please contact admin');
		}
    }

    public function update(Request $request, $id)
    {
        $this->validate($request,[
			'name'=>'required|max:255',
			'amount'=>'required|numeric',
			'mode'=>'required|max:255',
		]);
		

        try{
			$cheque = Cheque::find($id);

			if(Auth::user()->user_type==4)
	    	{
				$cheque = UserCheque::find($id);
			}
			$mode = $request->mode;
			
			if($mode == 2)
			{
				$this->validate($request,[
					'txn_date'=>'required|max:255',
				]);
				
				$date = $request->txn_date;
				$cheque->txn_date = date_format(date_create($date),"Y-m-d");
				$cheque->txn_no = $request->txn_no;
			}
			
			elseif($mode == 3)
			{
				$this->validate($request,[
					'acc_no'=>'required|numeric',
					'txn_date'=>'required|max:255',
				]);
				$date = $request->txn_date;
				$cheque->txn_date = date_format(date_create($date),"Y-m-d");
				$cheque->txn_no = $request->txn_no;
				$cheque->acc_no = $request->acc_no;
				$cheque->ifsc = $request->ifsc;
			}
			
			$date = $request->date;
			$cheque->date = date_format(date_create($date),"Y-m-d");
			$cheque->to_user = $request->name;
			$cheque->amount = $request->amount;
			$cheque->mode = $request->mode;
			$cheque->remark = $request->remarks;
			$cheque->user_sys = \Request::ip();
			$cheque->updated_by = Auth::id();
			$result = $cheque->save();
			
			//trans table
			if(Auth::user()->user_type==4){
				$transaction = UserTransaction::where('voucher_no', $expense->txn_no)->first();
				$transaction->credit = $request->amount;
				$transaction->user_sys = \Request::ip();
				$transaction->updated_by = Auth::id();
				$transaction->particulars = Auth::user()->name.' Deposit '.$request->amount.' to '.$request->name;
				$result2 = $transaction->save();
			}

			if($result){
				return redirect()->back()->with('success', 'Record updated successfully!');
			}
			else{
				return redirect()->back()->with('error', 'Something went wrong!');
			}
		}
    	catch(\Exception $e){
			$error = $e->getMessage();
		    return back()->with('error', 'Something went wrong! Please contact admin');
		}
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
    	try{
    		if(Auth::user()->user_type==4)
	    	{
				$cheque = UserCheque::find($id);
			}
			else
	        $cheque = Cheque::find($id);
	    
	        $result = $cheque->delete($id);
	        if($result){
				return redirect()->back()->with('success', 'Record deleted successfully!');
			}
			else{
				return redirect()->back()->with('error', 'Something went wrong!');
			}
		}
    	catch(\Exception $e){
			$error = $e->getMessage();
		    return back()->with('error', 'Something went wrong! Please contact admin');
		}
    }
	
	public function return(Request $request, $id)
    {
    	try{
    		$cheque = UserCheque::find($id);
	    	$string = $cheque->to_user;
			$return_balance = UserReturn::return_bal($string);
			$return_chk = UserReturn::where('txn_id', $id)->first();
			
			if(count($return_chk)>0){
				return redirect()->back()->with('warning', 'Return already exist! Please deposit more and try to return.');
			}
			
			$return = new UserReturn;
			$return->txn_id = $cheque->id;
			$return->by_user = $cheque->to_user;
			$return->amount = $return_balance;
			$return->mode = 'return';
			$return->user_sys = \Request::ip();
			$return->created_by = Auth::id();
			$return->updated_by = Auth::id();
			$result = $return->save();

			$return = UserReturn::find($return->id);
			$return->voucher_no = 'RTN'.$return->id;
			$result = $return->save();

			$transaction = new UserTransaction;
			$transaction->txn_type = 3;
			$transaction->voucher_no = $return->voucher_no;
			$transaction->debit = $return_balance;
			$transaction->balance = 0;
			$transaction->created_for = $cheque->to_user;
			$transaction->user_sys = \Request::ip();
			$transaction->updated_by = Auth::id();
			$transaction->created_by = Auth::id();
			$transaction->particulars = $cheque->to_user.' Return to '.Auth::user()->name;
			$result2 = $transaction->save();

	        if($result){
				return redirect()->back()->with('success', $return_balance.' returned successfully!');
			}
			else{
				return redirect()->back()->with('error', 'Something went wrong!');
			}
		}
    	catch(\Exception $e){
			$error = $e->getMessage();
		    return back()->with('error', 'Something went wrong! Please contact admin');
		}
    }

    public function payeename(Request $request)
    {
    	try{
    		$str = $request->term;
	    	$temp = UserCheque::where('to_user', 'like', '%' . $str . '%')->where('created_by', Auth::id())->pluck('to_user');
	    	return json_encode($temp);
    	}
    	catch(\Exception $e){
			$error = $e->getMessage();
		    return back()->with('error', 'Something went wrong! Please contact admin');
		}
    }
    
    public function payeeBalance(Request $request)
    {
    	try{
    		$nameofpayee = $request->created_for;
	    	$temp = Cheque::payeeBalance($nameofpayee);
	    	return json_encode($temp);
    	}
    	catch(\Exception $e){
			$error = $e->getMessage();
		    return back()->with('error', 'Something went wrong! Please contact admin');
		}
    }
}
