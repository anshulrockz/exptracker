<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PaymentNeft;
use App\Bank;
use App\Workshop;
use Auth;

class PaymentNeftController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index() 
    { 
    	$cheque = PaymentNeft::all_payment_nefts();
		$bank = Bank::all();
	    return view('payment-neft.index')->with(array('bank' => $bank, 'cheque' => $cheque));
    }

    public function create()
    {
		$location = Workshop::all();
		$voucher_no = PaymentNeft::lastid();
    	if(empty($voucher_no->id)) $voucher_no = 0;
    	else $voucher_no = $voucher_no->id;
    	$voucher_no = $voucher_no + 1;
    	$voucher_no = 'PLS_CHQ_'.$voucher_no;

		return view('payment-neft.create')->with(array('location' => $location, 'voucher_no' => $voucher_no, 'workshop' => $location ));
	}

    public function store(Request $request)
    {
    	$this->validate($request,[
			// 'date'=>'required|max:255',
			'amount'=>'required|numeric',
			'cheque_no'=>'required|max:255',
		]);
		
    	$voucher_no = PaymentNeft::lastid();
    	if(empty($voucher_no->id)) $voucher_no = 0;
    	else $voucher_no = $voucher_no->id;
    	$voucher_no = $voucher_no + 1;
    	$voucher_no = 'PLS_CHQ_'.$voucher_no;

		$cheque = new Cheque;
		$cheque->voucher_no = $voucher_no;
		$date = $request->cheque_date;
		$cheque->date = date_format(date_create($date),"Y-m-d"); 
		$cheque->cheque_no = $request->cheque_no;
		$cheque->party_name = $request->party_name;
		$cheque->amount = $request->amount;
		$cheque->mode = 2;
		$cheque->payment_status = 2;
		$cheque->remark = $request->remarks;
		$cheque->deposit_bank = $request->bank;
		if(Auth::user()->user_type == 5 || Auth::user()->user_type == 1)
		$cheque->location = $request->location;
		else
		$cheque->location = Auth::user()->workshop_id;
		// $cheque->acc_no = $request->acc_no;
		// $cheque->ifsc = $request->ifsc;
		// $cheque->txn_no = $request->txn_no;
		$cheque->user_sys = \Request::ip();
		$cheque->updated_by = Auth::id();
		$cheque->created_by = Auth::id();

		if(!empty($request->file('voucher_img')))
		{
			$image = $request->file('voucher_img');
			$image_name = time().'.'.$image->getClientOriginalExtension();
			$image->move(public_path('uploads/cheques/'), $image_name);
		    $cheque->voucher_img = $image_name;
		}

		$result = $cheque->save();
		
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
        	$cheque = PaymentNeft::find($id);
	        $userdetails = PaymentNeft::find($id)->UserDetails;
	        return view('payment-neft.show')->with(array('deposit' => $cheque, 'userdetails' => $userdetails));
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
	    		$cheque = UserPaymentNeft::find($id);
	    	}
	    	else
	    	{
		    	$cheque = PaymentNeft::find($id);
		    	$userdetails = PaymentNeft::find($id)->UserDetails;
		    }

	    	if(Auth::user()->user_type==1 || Auth::user()->user_type==5)
	    	{
	    		$companies = Company::all();
	    		$workshops = Workshop::all()->where('company',Auth::user()->company_id);
	        	return view('payment-neft.edit')->with(array('userdetails' => $userdetails, 'companies' => $companies, 'workshops' => $workshops, 'deposit' => $cheque,));
			}
			
	    	if(Auth::user()->user_type==3)
	    	{
	    		$workshops = Workshop::all()->where('company',Auth::user()->company_id);
	    		return view('payment-neft.edit')->with(array('workshops' => $workshops, 'deposit' => $cheque,'userdetails' => $userdetails));
			}

			if(Auth::user()->user_type==4)
	    	{
	    		
	    		return view('payment-neft.edit')->with(array( 'deposit' => $cheque));
			}

	        return view('payment-neft.edit')->with('deposit', $cheque);
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
			$cheque = PaymentNeft::find($id);

			if(Auth::user()->user_type==4)
	    	{
				$cheque = UserPaymentNeft::find($id);
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
				$transaction = UserTransaction::where('voucher_no', $cheque->txn_no)->first();
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

    public function destroy($id)
    {
    	$cheque = PaymentNeft::find($id);
	    
        $result = $cheque->delete($id);
        if($result){
			return redirect()->back()->with('success', 'Record deleted successfully!');
		}
		else{
			return redirect()->back()->with('error', 'Something went wrong!');
		}
    }
	
	public function payment(Request $request)
    {
    	$cheque = PaymentNeft::where('voucher_no',$request->voucher_no)->first(); 
        $cheque->deposit_bank = $request->bank;
        $cheque->remark = $request->remark;
        $date = $request->date;
		$cheque->date_deposit = date_format(date_create($date),"Y-m-d");
        $cheque->updated_by = Auth::id();
        $result = $cheque->save();

		if($result){
			return back()->with('success', 'Request completed successfully!');
		}
		else{
			return back()->with('error', 'Something went wrong!');
		}
    }

    public function changeStatus(Request $request)
    {
    	$cheque = PaymentNeft::find($request->id);
        $cheque->payment_status = $request->status;
        $cheque->updated_by = Auth::id();
        $result = $cheque->save();

		if($result){
			return back()->with('success', 'Request completed successfully!');
		}
		else{
			return back()->with('error', 'Something went wrong!');
		}
    }
}
