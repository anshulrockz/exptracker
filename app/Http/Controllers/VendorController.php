<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Vendor;
use Auth;

class VendorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
    	$vendor = Vendor::all();
        return view('vendor.index')->with('vendor',$vendor);
    }
    
    public function create()
    {
        return view('vendor.create');
    }

    public function store(Request $request)
    {
        $this->validate($request,[
			'name'=>'required|max:255',
		]);
		
		$vendor = new Vendor;
		$vendor->name = $request->name;
		$vendor->gst = $request->gst;
		$vendor->state = $request->state;
		$vendor->state_code = $request->state_code;
		$vendor->mobile = $request->mobile;
		$vendor->address = $request->address;
		$vendor->bank_name = $request->bank_name;
		$vendor->acc_no = $request->acc_no;
		$vendor->ifsc = $request->ifsc;
		$vendor->branch = $request->branch;
		$vendor->status = 1;
		$vendor->user_sys = \Request::ip();
		$vendor->updated_by = Auth::id();
		$vendor->created_by = Auth::id();
		
		$result = $vendor->save();
		
		if($result){
			return back()->with('success', 'Record added successfully!');
		}
		else{
			return back()->with('error', 'Something went wrong!');
		}
    }

    public function show($id)
    {
        $vendor = Vendor::find($id);
        return view('vendor.show')->with('vendor', $vendor);
    }

    public function edit($id)
    {
        $vendor = Vendor::find($id);
        return view('vendor.edit')->with('vendor', $vendor);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request,[
			'name'=>'required|max:255',
			'contact_person'=>'required|max:255',
			'mobile'=>'required|max:255',
			'cin'=>'required|max:255',
			'email'=>'required|max:255'
		]);
		
		$vendor = Vendor::find($id);
		$vendor->name = $request->name;
		$vendor->gst = $request->gst;
		$vendor->state = $request->state;
		$vendor->state_code = $request->state_code;
		$vendor->mobile = $request->mobile;
		$vendor->address = $request->address;
		$vendor->bank_name = $request->bank_name;
		$vendor->acc_no = $request->acc_no;
		$vendor->ifsc = $request->ifsc;
		$vendor->branch = $request->branch;
		$vendor->status = 1;
		$vendor->user_sys = \Request::ip();
		$vendor->updated_by = Auth::id();
		
		$result = $vendor->save();
		
		if($result){
			return redirect()->back()->with('success', 'Record updated successfully!');
		}
		else{
			return redirect()->back()->with('error', 'Something went wrong!');
		}
    }

    public function destroy($id)
    {
    	$count1 = User::all()->where('Vendor_id',$id)->count();
    	$count2 = Workshop::all()->where('vendor',$id)->count();
    	if($count1==0 && $count2==0){
			$vendor = Vendor::find($id);
        	$result = $vendor->delete($id);
        	
        	if($result){
				return redirect()->back()->with('success', 'Record deleted successfully!');
			}
			else{
				return redirect()->back()->with('error', 'Something went wrong!');
			}
		}
		
		else{
				return redirect()->back()->with('error', 'You cannot delete the Vendor because workshop & users exist in Vendor');
			}
        
    }
    
    public function id_ajax(Request $request)
    {
		$location = $request->id;
		$location = Vendor::where('workshop_id',$location)->get();//->pluck('name','id');
		print_r(json_encode($location));
	}
}
