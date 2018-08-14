<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ClaimDetail;
use App\ClaimJobDetail;
use App\ClaimJobEntry;
use App\CustomerDetail;
use App\Document;
use App\VehicleDetail;
use App\ClaimCategory;
use Auth;

class ClaimController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
    	$data = CustomerDetail::all();
        return view('claim.index')->with('data',$data);
    	// return view('claim.create');
    }
    
    public function create()
    {
    	$make = ClaimCategory::where('type_of_category','1')->get();
	    $type_of_customer = ClaimCategory::where('type_of_category','5')->get();
	    $type_of_vehicle = ClaimCategory::where('type_of_category','2')->get();
	    $doc_verification = ClaimCategory::where('type_of_category','3')->get();
	    $kyc_verification = ClaimCategory::where('type_of_category','4')->get();
	    $insurer = ClaimCategory::where('type_of_category','6')->get();
	    $surveyor = ClaimCategory::where('type_of_category','7')->get();
        return view('claim.create')->with(array('make' => $make, 'type_of_vehicle' => $type_of_vehicle, 'doc_verification' => $doc_verification, 'kyc_verification' => $kyc_verification, 'type_of_customer' => $type_of_customer, 'insurer' => $insurer, 'surveyor' => $surveyor));
    }

    public function store(Request $request)
    {
        $this->validate($request,[
			'name_of_insured'=>'required|max:255',
			'vehicle_num'=>'required|max:255',
			'accident_date'=>'required|max:255',
		]);
		
		$customer_details = new CustomerDetail;
		$customer_details->category = $request->category;
		$customer_details->name_of_insured = $request->name_of_insured;
		$customer_details->phone_of_insured = $request->phone_of_insured;
		$customer_details->email_of_insured = $request->email_of_insured;
		$customer_details->address_of_insured = $request->address_of_insured;
		$customer_details->status = 1;
		// $customer_details->user_sys = \Request::ip();
		$customer_details->updated_by = Auth::id();
		$customer_details->created_by = Auth::id();
		$result = $customer_details->save();

		$parent_id = $customer_details->id;

		$vehicle_details = new VehicleDetail;
		$vehicle_details->vehicle_num = $request->vehicle_num;
		$vehicle_details->chassis_num = $request->chassis_num;
		$vehicle_details->make_num = $request->make_num;
		$vehicle_details->model_num = $request->model_num;
		$vehicle_details->type_of_vehicle = $request->type_of_vehicle;
		$vehicle_details->status = 1;
		$vehicle_details->customer_detail_id = $parent_id;
		$vehicle_details->updated_by = Auth::id();
		$vehicle_details->created_by = Auth::id();
		$result = $vehicle_details->save();

		$claim_job_detail = new ClaimJobDetail;
		$date = $request->job_date;
		$claim_job_detail->job_date = date_format(date_create($date),"Y-m-d");
		$date = $request->invoice_date;
		$claim_job_detail->invoice_date = date_format(date_create($date),"Y-m-d");
		$date = $request->payment_date;
		$claim_job_detail->payment_date = date_format(date_create($date),"Y-m-d");
		$claim_job_detail->job_num = $request->job_num;
		$claim_job_detail->invoice_num = $request->invoice_num;
		$claim_job_detail->payment_amt = $request->payment_amt;
		$claim_job_detail->status = 1;
		$claim_job_detail->customer_detail_id = $parent_id;
		$claim_job_detail->updated_by = Auth::id();
		$claim_job_detail->created_by = Auth::id();
		$result = $claim_job_detail->save();

		$claim_detail = new ClaimDetail;

		$date = $request->accident_date;
		$claim_detail->accident_date = date_format(date_create($date),"Y-m-d");

		$date = $request->survey_date;
		$claim_detail->survey_date = date_format(date_create($date),"Y-m-d");

		$date = $request->reinspection_date;
		$claim_detail->reinspection_date = date_format(date_create($date),"Y-m-d");

		$claim_detail->insurer_name = $request->insurer_name;
		$claim_detail->surveyor_name = $request->surveyor_name;
		$claim_detail->insurer_num = $request->insurer_num;
		$claim_detail->surveyor_num = $request->surveyor_num;
		$claim_detail->office_add = $request->office_add;
		$claim_detail->surveyor_add = $request->surveyor_add;
		$claim_detail->policy_num = $request->policy_num;
		$claim_detail->claim_num = $request->claim_num;
		// $claim_detail->accident_date = $request->accident_date;
		$claim_detail->insured_amount = $request->insured_amount;
		$claim_detail->cost_of_repair = $request->cost_of_repair;
		// $claim_detail->survey_date = $request->survey_date;
		$claim_detail->survey_place = $request->survey_place;
		// $claim_detail->reinspection_date = $request->reinspection_date;
		$claim_detail->driver_name = $request->driver_name;
		$claim_detail->driver_licence_num = $request->driver_licence_num;
		$claim_detail->status = 1;
		$claim_detail->customer_detail_id = $parent_id;
		$claim_detail->updated_by = Auth::id();
		$claim_detail->created_by = Auth::id();
		$result = $claim_detail->save();

		if($request->hasFile('doc_file')){
			$i = 0;
			foreach ($request->doc_file as $key) {
				$document_details = new Document;
				$document_details->doc_type = $request->doc_type[$i];
				$document_details->doc_status = $request->doc_status[$i];
				$document_details->doc_category = $request->doc_category[$i];
				$document_details->status = 1;
				$document_details->customer_detail_id = $parent_id;
				$document_details->updated_by = Auth::id();
				$document_details->created_by = Auth::id();

				$image_name = time().'.'.$key->getClientOriginalExtension();
				$key->move(public_path('uploads/claims/'), $image_name);
			    $document_details->doc_file = $image_name;

				$result = $document_details->save();
				$i++;
			}
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
        $customer_details = CustomerDetail::find($id);
        $vehicle_details = VehicleDetail::where('customer_detail_id', $id)->first();
        $claim_job_detail = ClaimJobDetail::where('customer_detail_id', $id)->first();
        $claim_detail = ClaimDetail::where('customer_detail_id', $id)->first();
        $document_details_1 = Document::where('customer_detail_id', $id)
        							->where('doc_category', '1')->get();
        $document_details_2 = Document::where('customer_detail_id', $id)
        							->where('doc_category', '2')->get();
		$claim_job_entry = ClaimJobEntry::where('customer_detail_id', $id) ->get();

        $make = ClaimCategory::where('type_of_category','1')->get();
	    $type_of_customer = ClaimCategory::where('type_of_category','5')->get();
	    $type_of_vehicle = ClaimCategory::where('type_of_category','2')->get();
	    $doc_verification = ClaimCategory::where('type_of_category','3')->get();
	    $kyc_verification = ClaimCategory::where('type_of_category','4')->get();
	    $insurer = ClaimCategory::where('id', $claim_detail->insurer_name)->first();
	    $surveyor = ClaimCategory::where('id', $claim_detail->surveyor_name)->first();

        return view('claim.show')->with(array(
        	'make' => $make, 'type_of_vehicle' => $type_of_vehicle, 'doc_verification' => $doc_verification, 'kyc_verification' => $kyc_verification, 'type_of_customer' => $type_of_customer,
        	'customer_details' => $customer_details, 'vehicle_details' => $vehicle_details, 'claim_job_detail' => $claim_job_detail, 'claim_detail' => $claim_detail, 'document_details_1' => $document_details_1, 'document_details_2' => $document_details_2, 'insurer' => $insurer, 'surveyor' => $surveyor, 'claim_job_entry' => $claim_job_entry
        ));
    }

    public function edit($id)
    {
        $customer_details = CustomerDetail::find($id);
        $vehicle_details = VehicleDetail::where('customer_detail_id', $id)->first();
        $claim_job_detail = ClaimJobDetail::where('customer_detail_id', $id)->first();
        $claim_detail = ClaimDetail::where('customer_detail_id', $id)->first();
        $document_details_1 = Document::where('customer_detail_id', $id)
        							->where('doc_category', '1')->get();
        $document_details_2 = Document::where('customer_detail_id', $id)
        							->where('doc_category', '2')->get();
		$claim_job_entry = ClaimJobEntry::where('customer_detail_id', $id) ->get();

        $make = ClaimCategory::where('type_of_category','1')->get();
	    $type_of_customer = ClaimCategory::where('type_of_category','5')->get();
	    $type_of_vehicle = ClaimCategory::where('type_of_category','2')->get();
	    $doc_verification = ClaimCategory::where('type_of_category','3')->get();
	    $kyc_verification = ClaimCategory::where('type_of_category','4')->get();
	    $insurer = ClaimCategory::where('type_of_category','6')->get();
	    $surveyor = ClaimCategory::where('type_of_category','7')->get();

        return view('claim.edit')->with(array(
        	'make' => $make, 'type_of_vehicle' => $type_of_vehicle, 'doc_verification' => $doc_verification, 'kyc_verification' => $kyc_verification, 'type_of_customer' => $type_of_customer,
        	'customer_details' => $customer_details, 'vehicle_details' => $vehicle_details, 'claim_job_detail' => $claim_job_detail, 'claim_detail' => $claim_detail, 'document_details_1' => $document_details_1, 'document_details_2' => $document_details_2, 'insurer' => $insurer, 'surveyor' => $surveyor, 'claim_job_entry' => $claim_job_entry
        ));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request,[
			'name_of_insured'=>'required|max:255',
			'vehicle_num'=>'required|max:255',
			'accident_date'=>'required|max:255',
		]);
		
		$parent_id = $id;

		$customer_details = CustomerDetail::find($id);
		$customer_details->category = $request->category;
		$customer_details->name_of_insured = $request->name_of_insured;
		$customer_details->phone_of_insured = $request->phone_of_insured;
		$customer_details->email_of_insured = $request->email_of_insured;
		$customer_details->address_of_insured = $request->address_of_insured;
		$customer_details->status = 1;
		// $customer_details->user_sys = \Request::ip();
		$customer_details->updated_by = Auth::id();
		$customer_details->created_by = Auth::id();
		$result = $customer_details->save();

		$parent_id = $customer_details->id;

		$vehicle_details = VehicleDetail::where('customer_detail_id', $customer_details->id)->first();
		$vehicle_details->vehicle_num = $request->vehicle_num;
		$vehicle_details->chassis_num = $request->chassis_num;
		$vehicle_details->make_num = $request->make_num;
		$vehicle_details->model_num = $request->model_num;
		$vehicle_details->type_of_vehicle = $request->type_of_vehicle;
		$vehicle_details->status = 1;
		$vehicle_details->customer_detail_id = $parent_id;
		$vehicle_details->updated_by = Auth::id();
		$vehicle_details->created_by = Auth::id();
		$result = $vehicle_details->save();

		$claim_job_detail = ClaimJobDetail::where('customer_detail_id', $customer_details->id)->first();

		$date = $request->job_date;
		$claim_job_detail->job_date = date_format(date_create($date),"Y-m-d");

		$date = $request->invoice_date;
		$claim_job_detail->invoice_date = date_format(date_create($date),"Y-m-d");

		$date = $request->payment_date;
		$claim_job_detail->payment_date = date_format(date_create($date),"Y-m-d");

		$claim_job_detail->job_num = $request->job_num;
		$claim_job_detail->invoice_num = $request->invoice_num;
		$claim_job_detail->payment_amt = $request->payment_amt;
		$claim_job_detail->status = 1;
		$claim_job_detail->customer_detail_id = $parent_id;
		$claim_job_detail->updated_by = Auth::id();
		$claim_job_detail->created_by = Auth::id();
		$result = $claim_job_detail->save();

		$claim_detail = ClaimDetail::where('customer_detail_id', $customer_details->id)->first();

		$date = $request->accident_date;
		$claim_detail->accident_date = date_format(date_create($date),"Y-m-d");

		$date = $request->survey_date;
		$claim_detail->survey_date = date_format(date_create($date),"Y-m-d");

		$date = $request->reinspection_date;
		$claim_detail->reinspection_date = date_format(date_create($date),"Y-m-d");

		$claim_detail->insurer_name = $request->insurer_name;
		$claim_detail->surveyor_name = $request->surveyor_name;
		$claim_detail->insurer_num = $request->insurer_num;
		$claim_detail->surveyor_num = $request->surveyor_num;
		$claim_detail->office_add = $request->office_add;
		$claim_detail->surveyor_add = $request->surveyor_add;
		$claim_detail->policy_num = $request->policy_num;
		$claim_detail->claim_num = $request->claim_num;
		// $claim_detail->accident_date = $request->accident_date;
		$claim_detail->insured_amount = $request->insured_amount;
		$claim_detail->cost_of_repair = $request->cost_of_repair;
		// $claim_detail->survey_date = $request->survey_date;
		$claim_detail->survey_place = $request->survey_place;
		// $claim_detail->reinspection_date = $request->reinspection_date;
		$claim_detail->driver_name = $request->driver_name;
		$claim_detail->driver_licence_num = $request->driver_licence_num;
		$claim_detail->status = 1;
		$claim_detail->customer_detail_id = $parent_id;
		$claim_detail->updated_by = Auth::id();
		$claim_detail->created_by = Auth::id();
		$result = $claim_detail->save();

		for($i = 0; $i < count($request->entry_payment_amt); $i++){
			$claim_job_entry = new ClaimJobEntry;
			$date = $request->entry_payment_date[$i];
			$claim_job_entry->entry_payment_date = date_format(date_create($date),"Y-m-d");
			$claim_job_entry->entry_payment_amt = $request->entry_payment_amt[$i];
			$claim_job_entry->status = 1;
			$claim_job_entry->customer_detail_id = $parent_id;
			$claim_job_entry->updated_by = Auth::id();
			$claim_job_entry->created_by = Auth::id();
			$result = $claim_job_entry->save();
		}

		if($request->hasFile('doc_file')){
			$i = 0;
			foreach ($request->doc_file as $key) {
				$document_details = new Document;
				$document_details->doc_type = $request->doc_type[$i];
				$document_details->doc_status = $request->doc_status[$i];
				$document_details->doc_category = $request->doc_category[$i];
				$document_details->status = 1;
				$document_details->customer_detail_id = $parent_id;
				$document_details->updated_by = Auth::id();
				$document_details->created_by = Auth::id();

				$image_name = time().'.'.$key->getClientOriginalExtension();
				$key->move(public_path('uploads/claims/'), $image_name);
			    $document_details->doc_file = $image_name;

				$result = $document_details->save();
				$i++;
			}
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
    	$count1 = User::all()->where('company_id',$id)->count();
    	$count2 = Workshop::all()->where('company',$id)->count();
    	if($count1==0 && $count2==0){
			$company = Claim::find($id);
        	$result = $company->delete($id);
        	
        	if($result){
				return redirect()->back()->with('success', 'Record deleted successfully!');
			}
			else{
				return redirect()->back()->with('error', 'Something went wrong!');
			}
		}
		
		else{
				return redirect()->back()->with('error', 'You cannot delete the company because workshop & users exist in company');
			}
        
    }
    
    public function id_ajax(Request $request)
    {
		$location = $request->id;
		$location = Claim::where('workshop_id',$location)->get();//->pluck('name','id');
		print_r(json_encode($location));
	}
}
