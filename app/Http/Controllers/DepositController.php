<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Deposit;
use App\User;
use App\Transaction;
use App\Company;
use App\Workshop;
use Auth;

class DepositController extends Controller
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
    	
	    	$deposit = Deposit::all_deposits(); //->where('created_by',Auth::id());
        
        
        /*if(Auth::user()->user_type==2)
    	{
	    	$deposit = Deposit::workshop_deposits();
	        return view('deposit.index')->with('deposit',$deposit);
        }*/
        
        if(Auth::user()->user_type==3)
    	{
	    	$deposit = Deposit::workshop_deposits();
	        return view('deposit.index')->with('deposit',$deposit);
        }
        
        
	        return view('deposit.index')->with('deposit',$deposit);
    }

    public function create()
    {
    	if(Auth::user()->user_type==1 || Auth::user()->user_type==5)
    	{
    		$companies = Company::all();
        	return view('deposit.create')->with('companies',$companies);
		}
		
    	if(Auth::user()->user_type==3)
    	{
    		$users = User::all()->where('workshop_id',Auth::user()->workshop_id)->where('user_type','!=',1)->where('id','!=',Auth::id());
    		return view('deposit.create')->with('users',$users);
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
		
		$deposit = new Deposit;
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
				'ifsc'=>'required|max:255',
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
		$deposit->created_by = Auth::id();
		$result = $deposit->save();
		
		$id = $deposit->id;
		$deposit = Deposit::find($id);
		$deposit->txn_id = 'DPO'.$id;
		$result = $deposit->save();
		
		$transaction = new Transaction;
		$transaction->txn_date = date_format(date_create($date),"Y-m-d");
		$transaction->txn_type = 'Deposit';
		$transaction->txn_voucher = $deposit->txn_id;
		$transaction->voucher_no = $deposit->txn_id;
		$transaction->amt_added = $deposit->amount;
		$transaction->user_sys = \Request::ip();
		$transaction->updated_by = Auth::id();
		$transaction->created_by = Auth::id();
		$transaction->particulars = Auth::user()->name.' Transfered '.$deposit->amount.' to '.$request->name;
		$result2 = $transaction->save();
		
		$id2 = $transaction->id;
		$transaction = Transaction::find($id2);
		$transaction->txn_id = 'TXN_'.$id2;
		$result2 = $transaction->save();
		
		if($result){
			return back()->with('success', 'Record added successfully!');
		}
		else{
			return back()->with('error', 'Something went wrong!');
		}
    }

    public function show($id)
    {
        $deposit = Deposit::find($id);
        $userdetails = Deposit::find($id)->UserDetails;
        return view('deposit.show')->with(array('deposit' => $deposit, 'userdetails' => $userdetails));
    }

    public function edit($id)
    {
    	$deposit = Deposit::find($id);
    	$userdetails = Deposit::find($id)->UserDetails;
    	
    	if(Auth::user()->user_type==1 || Auth::user()->user_type==5)
    	{
    		$companies = Company::all();
    		$workshops = Workshop::all()->where('company',Auth::user()->company_id);
        	return view('deposit.edit')->with(array('userdetails' => $userdetails, 'companies' => $companies, 'workshops' => $workshops, 'deposit' => $deposit,));
		}
		
    	if(Auth::user()->user_type==3)
    	{
    		$workshops = Workshop::all()->where('company',Auth::user()->company_id);
    		return view('deposit.edit')->with(array('workshops' => $workshops, 'deposit' => $deposit,'userdetails' => $userdetails));
		}
        $deposit = Deposit::find($id);
        return view('deposit.edit')->with('deposit', $deposit);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request,[
			'name'=>'required|max:255',
			'amount'=>'required|numeric',
			'mode'=>'required|max:255',
		]);
		
		$deposit = Deposit::find($id);
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
		
		if($result){
			return redirect()->back()->with('success', 'Record updated successfully!');
		}
		else{
			return redirect()->back()->with('error', 'Something went wrong!');
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
        $deposit = Deposit::find($id);
        $result = $deposit->delete($id);
        if($result){
			return redirect()->back()->with('success', 'Record deleted successfully!');
		}
		else{
			return redirect()->back()->with('error', 'Something went wrong!');
		}
    }
}
