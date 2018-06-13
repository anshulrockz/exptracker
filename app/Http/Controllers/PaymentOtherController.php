<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PaymentOther;
use App\Bank;
use App\Workshop;
use Auth;

class PaymentOtherController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index() 
    { 
    	$paymentother = PaymentOther::all_payment_others();
		$bank = Bank::all();
	    return view('payment-other.index')->with(array('bank' => $bank, 'data' => $paymentother));
    }

    public function create()
    {
		$location = Workshop::all();
		$voucher_no = PaymentOther::lastid();
    	if(empty($voucher_no->id)) $voucher_no = 0;
    	else $voucher_no = $voucher_no->id;
    	$voucher_no = $voucher_no + 1;
    	$voucher_no = 'PLS_CASH_'.date('ym').sprintf("%03d", $voucher_no);

		return view('payment-other.create')->with(array('location' => $location, 'voucher_no' => $voucher_no, 'workshop' => $location ));
	}

    public function store(Request $request)
    {
    	$this->validate($request,[
			// 'date'=>'required|max:255',
			'amount'=>'required|numeric',
		]);
		
    	$voucher_no = PaymentOther::lastid();
    	if(empty($voucher_no->id)) $voucher_no = 0;
    	else $voucher_no = $voucher_no->id;
    	$voucher_no = $voucher_no + 1;
    	$voucher_no = 'PLS_CHQ_'.date('ym').sprintf("%03d", $voucher_no);

		$paymentother = new PaymentOther;
		$paymentother->voucher_no = $voucher_no;
		$date = $request->cash_date;
		$paymentother->date = date_format(date_create($date),"Y-m-d");
		$paymentother->ref_no = $request->ref_no;
		$paymentother->party_name = $request->party_name;
		$paymentother->amount = $request->amount;
		$paymentother->mode = $request->mode;
		$paymentother->payment_status = $request->mode;
		$paymentother->remark = $request->remarks;
		$paymentother->deposit_bank = $request->bank;
		if(Auth::user()->user_type == 5 || Auth::user()->user_type == 1)
		$paymentother->location = $request->location;
		else
		$paymentother->location = Auth::user()->workshop_id;
		$paymentother->acc_no = $request->acc_no;
		$paymentother->ifsc = $request->ifsc;
		$paymentother->txn_no = $request->txn_no;
		$paymentother->user_sys = \Request::ip();
		$paymentother->updated_by = Auth::id();
		$paymentother->created_by = Auth::id();

		if(!empty($request->file('voucher_img')))
		{
			$image = $request->file('voucher_img');
			$image_name = time().'.'.$image->getClientOriginalExtension();
			$image->move(public_path('uploads/others/'), $image_name);
		    $paymentother->voucher_img = $image_name;
		}

		$result = $paymentother->save();
		
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
        	$paymentother = PaymentOther::find($id);
	        $userdetails = PaymentOther::find($id)->UserDetails;
	        return view('payment-other.show')->with(array('deposit' => $paymentother, 'userdetails' => $userdetails));
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
	    		$paymentother = UserPaymentOther::find($id);
	    	}
	    	else
	    	{
		    	$paymentother = PaymentOther::find($id);
		    	$userdetails = PaymentOther::find($id)->UserDetails;
		    }

	    	if(Auth::user()->user_type==1 || Auth::user()->user_type==5)
	    	{
	    		$companies = Company::all();
	    		$workshops = Workshop::all()->where('company',Auth::user()->company_id);
	        	return view('payment-other.edit')->with(array('userdetails' => $userdetails, 'companies' => $companies, 'workshops' => $workshops, 'deposit' => $paymentother,));
			}
			
	    	if(Auth::user()->user_type==3)
	    	{
	    		$workshops = Workshop::all()->where('company',Auth::user()->company_id);
	    		return view('payment-other.edit')->with(array('workshops' => $workshops, 'deposit' => $paymentother,'userdetails' => $userdetails));
			}

			if(Auth::user()->user_type==4)
	    	{
	    		
	    		return view('payment-other.edit')->with(array( 'deposit' => $paymentother));
			}

	        return view('payment-other.edit')->with('deposit', $paymentother);
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
			$paymentother = PaymentOther::find($id);

			if(Auth::user()->user_type==4)
	    	{
				$paymentother = UserPaymentOther::find($id);
			}
			$mode = $request->mode;
			
			if($mode == 2)
			{
				$this->validate($request,[
					'txn_date'=>'required|max:255',
				]);
				
				$date = $request->txn_date;
				$paymentother->txn_date = date_format(date_create($date),"Y-m-d");
				$paymentother->txn_no = $request->txn_no;
			}
			
			elseif($mode == 3)
			{
				$this->validate($request,[
					'acc_no'=>'required|numeric',
					'txn_date'=>'required|max:255',
				]);
				$date = $request->txn_date;
				$paymentother->txn_date = date_format(date_create($date),"Y-m-d");
				$paymentother->txn_no = $request->txn_no;
				$paymentother->acc_no = $request->acc_no;
				$paymentother->ifsc = $request->ifsc;
			}
			
			$date = $request->date;
			$paymentother->date = date_format(date_create($date),"Y-m-d");
			$paymentother->to_user = $request->name;
			$paymentother->amount = $request->amount;
			$paymentother->mode = $request->mode;
			$paymentother->remark = $request->remarks;
			$paymentother->user_sys = \Request::ip();
			$paymentother->updated_by = Auth::id();
			$result = $paymentother->save();
			
			//trans table
			if(Auth::user()->user_type==4){
				$transaction = UserTransaction::where('voucher_no', $paymentother->txn_no)->first();
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
    	$paymentother = PaymentOther::find($id);
	    
        $result = $paymentother->delete($id);
        if($result){
			return redirect()->back()->with('success', 'Record deleted successfully!');
		}
		else{
			return redirect()->back()->with('error', 'Something went wrong!');
		}
    }
	
	public function payment(Request $request)
    {
    	$paymentother = PaymentOther::where('voucher_no',$request->voucher_no)->first(); 
        $paymentother->deposit_bank = $request->bank;
        $paymentother->remark = $request->remark;
        $date = $request->date;
		$paymentother->date_deposit = date_format(date_create($date),"Y-m-d");
        $paymentother->updated_by = Auth::id();
        $result = $paymentother->save();

		if($result){
			return back()->with('success', 'Request completed successfully!');
		}
		else{
			return back()->with('error', 'Something went wrong!');
		}
    }

    public function changeStatus($id)
    {
    	$paymentother = PaymentOther::find($id);
        $paymentother->payment_status = 1;
        $paymentother->updated_by = Auth::id();
        $result = $paymentother->save();

		if($result){
			return back()->with('success', 'Request completed successfully!');
		}
		else{
			return back()->with('error', 'Something went wrong!');
		}
    }
}
