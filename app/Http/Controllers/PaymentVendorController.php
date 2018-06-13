<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PaymentVendor;
use App\Vendor;
use App\Transaction;
use App\Company;
use App\Workshop;
use Auth;

class PaymentVendorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
    	$deposit = PaymentVendor::all_deposits(); //->where('created_by',Auth::id());
    
        /*if(Auth::user()->user_type==2)
    	{
	    	$deposit = PaymentVendor::workshop_deposits();
	        return view('payment-vendor.index')->with('deposit',$deposit);
        }*/
        
        if(Auth::user()->user_type==3)
    	{
	    	$deposit = PaymentVendor::workshop_deposits();
	        return view('payment-vendor.index')->with('deposit',$deposit);
        }
        
    	$deposit = PaymentVendor::all_deposits();
        return view('payment-vendor.index')->with('deposit',$deposit);
    
    
        return view('payment-vendor.index')->with('deposit',$deposit);    	
    }

    public function create()
    {
    	try{
    		if(Auth::user()->user_type==1 || Auth::user()->user_type==5)
	    	{
	    		$companies = Company::all();
	        	return view('payment-vendor.create')->with('companies',$companies);
			}
			
	    	if(Auth::user()->user_type==3)
	    	{
	    		$users = Bank::all()->where('workshop_id',Auth::user()->workshop_id)->where('user_type','!=',1)->where('id','!=',Auth::id());
	    		return view('payment-vendor.create')->with('users',$users);
			}

			if(Auth::user()->user_type==4)
	    	{
	    		$balance = Expense::balance(Auth::id());
				$users = Bank::all()->where('workshop_id',Auth::user()->workshop_id)->where('user_type',4)->where('id','!=',Auth::id());
	    		return view('payment-vendor.create')->with(array('users' => $users, 'balance' => $balance ));
			}
		}
    	catch(\Exception $e){
			$error = $e->getMessage();
		    return back()->with('error', 'Something went wrong! Please contact admin');
		}
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    	

    	$this->validate($request,[
			'name'=>'required|max:255',
			'date'=>'required|max:255',
			'amount'=>'required|numeric',
			'mode'=>'required|max:255',
		]);
		
		try{
			$deposit = new PaymentVendor;

			if(Auth::user()->user_type==4)
	    	{
	    		$balance = Expense::balance(Auth::id());
		    	if($balance < $request->amount){
		    	    return back()->with('warning', 'Request failed! Amount cannot be greater than balance.');
		    	}

				$deposit = new BankPaymentVendor;
			}

			$mode = $request->mode;
			
			if($mode == 2)
			{
				//$date = $request->txn_date;
				//$deposit->txn_date = date_format(date_create($date),"Y-m-d");
				$deposit->txn_no = $request->txn_no;
			}
			
			elseif($mode == 3)
			{
				$deposit->txn_no = $request->txn_no;
				$deposit->acc_no = $request->acc_no;
				$deposit->ifsc = $request->ifsc;
			}
			
			$date = $request->date;
			$deposit->date = date_format(date_create($date),"Y-m-d");
			$deposit->to_user = $request->name;
			$deposit->amount = $request->amount;
			$deposit->mode = $request->mode;
			$deposit->remark = $request->remarks;
			$deposit->user_sys = \Request::ip();
			$deposit->updated_by = Auth::id();
			$deposit->created_by = Auth::id();
			$result = $deposit->save();
			
			$id = $deposit->id;

			if(Auth::user()->user_type==4)
	    	{
				$deposit = BankPaymentVendor::find($id);
			}
			else
				$deposit = PaymentVendor::find($id);

			$deposit->txn_id = 'DPO'.$id;
			$result = $deposit->save();
			
			//trans table
			if(Auth::user()->user_type==4){
				$transaction = new BankTransaction;
				$transaction->txn_type = 1;
				$transaction->voucher_no = $deposit->txn_id;
				$transaction->credit = $request->amount;
				$transaction->balance = PaymentVendor::payeeBalance($request->name);
				$transaction->created_for = $request->name;
				$transaction->user_sys = \Request::ip();
				$transaction->updated_by = Auth::id();
				$transaction->created_by = Auth::id();
				$transaction->particulars = Auth::user()->name.' PaymentVendor to '.$request->name;
				$result2 = $transaction->save();
			}
			
			if($result){
				return back()->with('success', 'Record added successfully!');
			}
			else{
				return back()->with('error', 'Something went wrong!');
			}
		}
    	catch(\Exception $e){
			$error = $e->getMessage();
		    return back()->with('error', 'Something went wrong! Please contact admin');
		}
    }

    public function show($id)
    {
        try{
        	$deposit = PaymentVendor::find($id);
	        $userdetails = PaymentVendor::find($id)->BankDetails;
	        return view('payment-vendor.show')->with(array('deposit' => $deposit, 'userdetails' => $userdetails));
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
	    		$deposit = BankPaymentVendor::find($id);
	    	}
	    	else
	    	{
		    	$deposit = PaymentVendor::find($id);
		    	$userdetails = PaymentVendor::find($id)->BankDetails;
		    }

	    	if(Auth::user()->user_type==1 || Auth::user()->user_type==5)
	    	{
	    		$companies = Company::all();
	    		$workshops = Workshop::all()->where('company',Auth::user()->company_id);
	        	return view('payment-vendor.edit')->with(array('userdetails' => $userdetails, 'companies' => $companies, 'workshops' => $workshops, 'deposit' => $deposit,));
			}
			
	    	if(Auth::user()->user_type==3)
	    	{
	    		$workshops = Workshop::all()->where('company',Auth::user()->company_id);
	    		return view('payment-vendor.edit')->with(array('workshops' => $workshops, 'deposit' => $deposit,'userdetails' => $userdetails));
			}

			if(Auth::user()->user_type==4)
	    	{
	    		
	    		return view('payment-vendor.edit')->with(array( 'deposit' => $deposit));
			}

	        return view('payment-vendor.edit')->with('deposit', $deposit);
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
			$deposit = PaymentVendor::find($id);

			if(Auth::user()->user_type==4)
	    	{
				$deposit = BankPaymentVendor::find($id);
			}
			$mode = $request->mode;
			
			if($mode == 2)
			{
				$this->validate($request,[
					'txn_date'=>'required|max:255',
				]);
				
				$date = $request->txn_date;
				$deposit->txn_date = date_format(date_create($date),"Y-m-d");
				$deposit->txn_no = $request->txn_no;
			}
			
			elseif($mode == 3)
			{
				$this->validate($request,[
					'acc_no'=>'required|numeric',
					'txn_date'=>'required|max:255',
				]);
				$date = $request->txn_date;
				$deposit->txn_date = date_format(date_create($date),"Y-m-d");
				$deposit->txn_no = $request->txn_no;
				$deposit->acc_no = $request->acc_no;
				$deposit->ifsc = $request->ifsc;
			}
			
			$date = $request->date;
			$deposit->date = date_format(date_create($date),"Y-m-d");
			$deposit->to_user = $request->name;
			$deposit->amount = $request->amount;
			$deposit->mode = $request->mode;
			$deposit->remark = $request->remarks;
			$deposit->user_sys = \Request::ip();
			$deposit->updated_by = Auth::id();
			$result = $deposit->save();
			
			//trans table
			if(Auth::user()->user_type==4){
				$transaction = BankTransaction::where('voucher_no', $expense->txn_no)->first();
				$transaction->credit = $request->amount;
				$transaction->user_sys = \Request::ip();
				$transaction->updated_by = Auth::id();
				$transaction->particulars = Auth::user()->name.' PaymentVendor '.$request->amount.' to '.$request->name;
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
				$deposit = BankPaymentVendor::find($id);
			}
			else
	        $deposit = PaymentVendor::find($id);
	    
	        $result = $deposit->delete($id);
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
    		$deposit = BankPaymentVendor::find($id);
	    	$string = $deposit->to_user;
			$return_balance = BankReturn::return_bal($string);
			$return_chk = BankReturn::where('txn_id', $id)->first();
			
			if(count($return_chk)>0){
				return redirect()->back()->with('warning', 'Return already exist! Please deposit more and try to return.');
			}
			
			$return = new BankReturn;
			$return->txn_id = $deposit->id;
			$return->by_user = $deposit->to_user;
			$return->amount = $return_balance;
			$return->mode = 'return';
			$return->user_sys = \Request::ip();
			$return->created_by = Auth::id();
			$return->updated_by = Auth::id();
			$result = $return->save();

			$return = BankReturn::find($return->id);
			$return->voucher_no = 'RTN'.$return->id;
			$result = $return->save();

			$transaction = new BankTransaction;
			$transaction->txn_type = 3;
			$transaction->voucher_no = $return->voucher_no;
			$transaction->debit = $return_balance;
			$transaction->balance = 0;
			$transaction->created_for = $deposit->to_user;
			$transaction->user_sys = \Request::ip();
			$transaction->updated_by = Auth::id();
			$transaction->created_by = Auth::id();
			$transaction->particulars = $deposit->to_user.' Return to '.Auth::user()->name;
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
	    	$temp = BankPaymentVendor::where('to_user', 'like', '%' . $str . '%')->where('created_by', Auth::id())->pluck('to_user');
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
	    	$temp = PaymentVendor::payeeBalance($nameofpayee);
	    	return json_encode($temp);
    	}
    	catch(\Exception $e){
			$error = $e->getMessage();
		    return back()->with('error', 'Something went wrong! Please contact admin');
		}
    }

    public function id_ajax(Request $request)
    {
		try{
			$workshop_id = $request->id;
			$employee = Vendor::where([['location',$workshop_id],['id','!=',Auth::id()]])->get();
			print_r(json_encode($employee));
		}
    	catch(\Exception $e){
			$error = $e->getMessage();
		    return back()->with('error', 'Something went wrong! Please contact admin');
		}
	}

}
