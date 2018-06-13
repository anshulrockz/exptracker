<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Expense;
use App\ExpenseDetail;
use App\Deposit;
use App\Workshop;
use App\Vendor;
use App\Transaction;
use App\UserTransaction;
use App\ExpenseCategory;
use App\PurchaseCategory;
use App\Description;
use App\Tax;
use App\SubExpense;
use Auth;

class ExpenseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->expense = new Expense();
    }
    
    public function index()
    {	
    	if(Auth::user()->user_type==1  || Auth::user()->user_type== 5)
    	{
			$expense = Expense::super_admin_all();
			return view('expense.index')->with('expense',$expense);
		}
    	
    	
    	if(Auth::user()->user_type == 3)
    	{
			$expense = $this->expense->workshop_all();
			//$balance = $this->expense->balance(); //dd($expense);
	        return view('expense.index')->with('expense',$expense);
		}
		
		if(Auth::user()->user_type == 4)
    	{
			$expense = $this->expense->user_all();
			//$balance = $this->expense->balance(); //dd($expense);
	        return view('expense.index')->with('expense',$expense);
		}
   }

    public function create()
    {
	  //   	if(Auth::user()->user_type == 1)
	  //   	{
	  //   		$expense = Expense::super_admin_all();
		 //    	return view('expense.index')->with('expense',$expense);
			// }
			// else
	  //   	{
		    	$expense_category = ExpenseCategory::orderBy('name', 'ASC')->get();
		    	$description = Description::all();
		    	$tax = Tax::all();
			// }

	    	$workshop = Workshop::all();

	    	if(Auth::user()->user_type == 1 || Auth::user()->user_type == 5)
	    	$vendor = Vendor::all();
	    	else
	    	$vendor = Vendor::where('location', Auth::user()->workshop_id)->get();
	    	
	    	$balance = $this->expense->balance(Auth::id());
	    	
	    	$voucher_no = $this->expense->lastid();
	    	if(empty($voucher_no)) $voucher_no == 0;
	    	else $voucher_no = $voucher_no->id;
	    	$voucher_no = $voucher_no + 1;
	    	$voucher_no = 'PLS_ET_'.date('ym').sprintf("%03d", $voucher_no);

	        return view('expense.create')->with(array( 'expense_category' => $expense_category, 'description' => $description, 'tax' => $tax, 'balance' => $balance, 'voucher_no' => $voucher_no, 'workshop' => $workshop, 'vendor' => $vendor));
    }

    public function store(Request $request)
    {
    	$this->validate($request,[
			'vendor_id'=>'required|max:255',
			'total_amount'=>'required|min:1',
		]);
	    	
    	$balance = Expense::balance(Auth::id());

        if($request->created_for){
        	$nameofpayee = $request->created_for;
	    	$balance = Deposit::payeeBalance($request->created_for);
	    	if($balance < $request->total_amount){
	    	    return back()->with('warning', 'Request failed! Expense amount cannot be greater than balance.');
	    	}
        }

		$expense = new Expense;
		$expense->mode = $request->mode;
		$expense->paid_in  = $request->mode;

		if($balance < $request->total_amount){
			$expense->mode = 2;
			$expense->paid_in = 2;
		}

		if($expense->mode == 1){
			$expense->paid_by = Auth::id();
		}

		if(Auth::user()->user_type == 5 || Auth::user()->user_type == 1)
		$expense->location = $request->location;
		else
		$expense->location = Auth::user()->workshop_id;

		if(!empty($request->file('voucher_img')))
		{
			$image = $request->file('voucher_img');
			$image_name = time().'.'.$image->getClientOriginalExtension();
			$image->move(public_path('uploads/expenses/'), $image_name);
		    $expense->voucher_img = $image_name;
		}
		

		$vendor = Vendor::find($request->vendor_id);

		$date = $request->invoice_date;
		$expense->invoice_date = date_format(date_create($date),"Y-m-d");
		$expense->invoice_no = $request->invoice_no;
		$expense->created_for = $request->created_for;
		$expense->vendor_id = $request->vendor_id;
		$expense->party_name = $vendor->name;
		$expense->party_gstin = $vendor->gst;
		// $expense->party_name = $request->party_name;
		// $expense->party_gstin = $request->party_gstin;
		$expense->amount = $request->total_amount;
		$expense->round_off = $request->round_off;
		$expense->inv_type = $request->tax_type;			
		$expense->status = 1;
		$expense->user_sys = \Request::ip();
		$expense->updated_by = Auth::id();
		$expense->created_by = Auth::id();
		
		$result = $expense->save();
		
		$amount = 0;

		for($i = 0; $i < count($request->cost); $i++){
			$expense_details = new ExpenseDetail;
			$expense_details->expense_id = $expense->id;
			$expense_details->category1 = $request->type[$i];
			$expense_details->category2 = $request->category[$i];
			$expense_details->category3 = $request->expense_category[$i];
			$expense_details->description = $request->description[$i];
			$expense_details->reason = $request->reason[$i];
			$expense_details->code = $request->code[$i];
			$expense_details->cost = $request->cost[$i];
			$expense_details->quantity = $request->quantity[$i];
			$expense_details->tax_value = $request->tax[$i];
			$expense_details->sgst = $request->sgst[$i];
			$expense_details->cgst = $request->cgst[$i];
			$expense_details->igst = $request->igst[$i];
			$expense_details->user_sys = \Request::ip();
			$expense_details->updated_by = Auth::id();
			$expense_details->created_by = Auth::id();
			$expense_details->save();
			$amount += ($request->cost[$i]*$request->quantity[$i]) + $request->sgst[$i] +  $request->cgst[$i] + $request->igst[$i];
		}
		
		$expense = Expense::find($expense->id);
		$expense->voucher_no = 'PLS_ET_'.date('ym').sprintf("%04d", $expense->id);
		$expense->amount = $amount;
		$result = $expense->save();

		$transaction = new Transaction;
		$transaction->txn_type = 1;  	//1- Expense, 2- Payment
		$transaction->voucher_no = $expense->voucher_no;
		$transaction->invoice_no = $expense->invoice_no;
		$transaction->invoice_date = $expense->invoice_date;
		$transaction->vendor_id = $expense->vendor_id;
		if($expense->mode = 1)
		$transaction->debit = $amount;
		if($expense->mode = 2)
		$transaction->credit = $amount;
		$transaction->particulars = 'Spent for expense '.$expense->voucher_no;
		$transaction->user_sys = \Request::ip();
		$transaction->updated_by = Auth::id();
		$transaction->created_by = Auth::id();
		$result2 = $transaction->save();

		$transaction = Transaction::find($transaction->id);
		$transaction->voucher_no = 'PLS_TXN_'.date('ym').sprintf("%04d", $transaction->id);
		$result = $transaction->save();

		//trans table
		if($request->created_for && Auth::user()->user_type==4){
			$user_transaction = new UserTransaction;
			$user_transaction->txn_type = 2;
			$user_transaction->voucher_no = $expense->voucher_no;
			$user_transaction->debit = $amount;
			$user_transaction->balance = $balance-$amount;
			$user_transaction->created_for = $expense->created_for;
			$user_transaction->user_sys = \Request::ip();
			$user_transaction->updated_by = Auth::id();
			$user_transaction->created_by = Auth::id();
			$user_transaction->particulars = $request->created_for.' Spent for expense '.$expense->voucher_no;
			$result2 = $user_transaction->save();
		}

		if($result){
			return back()->with('success', 'Record added successfully! Your expense ID:'.$expense->voucher_no);
		}
		else{
			return back()->with('error', 'Something went wrong!');
		}
    }

    public function show($id)
    {
    	try{
	        $expense = Expense::find($id);
	        $userdetails = Expense::find($id)->UserDetails();
	        return view('expense.show')->with(array('expense' => $expense, 'userdetails' => $userdetails));
	    }
    	catch(\Exception $e){
			$error = $e->getMessage();
		    return back()->with('error', 'Something went wrong! Please contact admin');
		}
    }

    public function edit($id)
    {
    	try{
	    	$description = Description::all();
	    	$tax = Tax::all();
	    	$expense_category = ExpenseCategory::orderBy('name', 'ASC')->get();
	    	$purchase_category = PurchaseCategory::all();
	        $expense = Expense::find($id);
	    	$workshop = Workshop::all();
	        $expense_details = Expense::find($id)->ExpenseDetails; 
	    	$balance = $this->expense->balance($expense->created_by); //dd($balance );
	        return view('expense.edit')->with(array('expense' => $expense, 'expense_category' => $expense_category, 'purchase_category' => $purchase_category, 'balance' => $balance, 'expense_details' => $expense_details, 'description' => $description, 'tax' => $tax, 'workshop' => $workshop) );
	    }
    	catch(\Exception $e){
			$error = $e->getMessage();
		    return back()->with('error', 'Something went wrong! Please contact admin');
		}
    }

    public function update(Request $request, $id)
    {
        $this->validate($request,[
			'party_name'=>'required|max:255',
			'total_amount'=>'required|min:1',
		]);

    	if($request->created_for){
        	$nameofpayee = $request->created_for;
	    	$payeeBalance = Deposit::payeeBalance($nameofpayee);
	    	$balance = $payeeBalance;

	    	if($payeeBalance < $request->total_amount){
	    	    return back()->with('warning', 'Request failed! Expense amount cannot be greater than balance.');
	    	}
        }

		$expense = Expense::find($id);
		$date = $request->invoice_date;
		$expense->invoice_date = date_format(date_create($date),"Y-m-d");
		$expense->invoice_no = $request->invoice_no;
		$expense->party_name = $request->party_name;
		$expense->party_gstin = $request->party_gstin;
		$expense->paid_in = $request->mode;
		$expense->amount = $request->total_amount;
		$expense->round_off = $request->round_off;
		
		if(Auth::user()->user_type == 1 || Auth::user()->user_type == 5)
		$expense->location = $request->location;
		// else
		// $expense->location = Auth::user()->workshop_id;

		if($expense->mode == 1){
			$expense->paid_by = Auth::id();
		}

		if(!empty($request->file('voucher_img')))
		{
			$image = $request->file('voucher_img');
			$image_name = time().'.'.$image->getClientOriginalExtension();
			$image->move(public_path('uploads/expenses/'), $image_name);
		    $expense->voucher_img = $image_name;
		}
		
		$expense->status = 1;
		$expense->user_sys = \Request::ip();
		$expense->updated_by = Auth::id();
		
		$result = $expense->save();

		$id = $expense->id;

		$amount = $expense->amount;
		$detailid = $request->detailid;
		$supply_type = $request->type;
		$supply_category = $request->category;
		$expense_category = $request->expense_category;
		$description = $request->description;
		$reason = $request->reason;
		$code = $request->code;
		$cost = $request->cost;
		$quantity = $request->quantity;
		$tax = $request->tax;
		$sgst = $request->sgst;
		$cgst = $request->cgst;
		$igst = $request->igst;
		
		if(isset($request->delRow))
		{
			$delRow = $request->delRow;

			for($i = 0; $i < count($delRow); $i++)
			{
				$expense_details = ExpenseDetail::find($delRow[$i]);
	    	    $expense_details->delete($delRow[$i]);
	    	}
    	}

		for($i = 0; $i < count($cost); $i++){
			
			$expense_details = new ExpenseDetail;
			$expense_details->expense_id = $id;
			$expense_details->category1 = $supply_type[$i];
			$expense_details->category2 = $supply_category[$i];
			$expense_details->category3 = $expense_category[$i];
			$expense_details->description = $description[$i];
			$expense_details->reason = $reason[$i];
			$expense_details->code = $code[$i];
			$expense_details->cost = $cost[$i];
			$expense_details->quantity = $quantity[$i];
			$expense_details->tax_value = $tax[$i];
			$expense_details->sgst = $sgst[$i];
			$expense_details->cgst = $cgst[$i];
			$expense_details->igst = $igst[$i];
			$expense_details->user_sys = \Request::ip();
			$expense_details->updated_by = Auth::id();
			$expense_details->created_by = Auth::id();
			$expense_details->save();
			$amount += ($cost[$i]*$quantity[$i]) + $sgst[$i] +  $cgst[$i] + $igst[$i];
		}
		if(Auth::user()->user_type==4 && !empty($expense->created_for)){
			$transaction = UserTransaction::where('voucher_no', $expense->voucher_no)->first();
			$transaction->debit = $expense->total_amount;
			$transaction->balance = Deposit::payeeBalance($expense->created_for);
			$transaction->user_sys = \Request::ip();
			$transaction->updated_by = Auth::id();
			$transaction->particulars = Auth::user()->name.' spent '.$expense->amount.' to purchase '.$expense->subject;
			$result2 = $transaction->save();
		}
		
		if($result){
			return redirect()->back()->with('success', 'Record updated successfully!');
		}
		else{
			return redirect()->back()->with('error', 'Something went wrong!');
		}
    }

    public function destroy($id)
    {
    	try{
	        $expense = Expense::find($id);
	        $result = $expense->delete($id);
	        
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

    public function cancel($id)
    {
    	try{
	        $expense = Expense::find($id);
	        $expense->status = 2;
	        $result = $expense->save();
	        
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

	public function changetopaid($id)
    {
    	try{
	    	//dd($id);
	        $expense = Expense::find($id);
	        $expense->mode = 1;
	        $result = $expense->save();
	        
	        if($result){
				return redirect()->back()->with('success', 'Paid successfully!');
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

    public function partyname(Request $request)
    { 
    	try{
    		$str = $request->term;
	        $temp = Expense::Where('party_name', 'like', '%' . $str . '%')->pluck('party_name');

	        return json_encode($temp);
	    }
    	catch(\Exception $e){
			$error = $e->getMessage();
		    return back()->with('error', 'Something went wrong! Please contact admin');
		}
    }

    public function partyGSTIN(Request $request)
    { 
    	try{
    		$str = $request->party_name;
	        $temp = Expense::where('party_name', $str)->pluck("party_gstin")->first();

	        return json_encode($temp);
	    }
    	catch(\Exception $e){
			$error = $e->getMessage();
		    return back()->with('error', 'Something went wrong! Please contact admin');
		}
    }

}
