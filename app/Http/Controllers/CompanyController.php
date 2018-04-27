<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Company;
use App\Workshop;
use App\User;
use Auth;

class CompanyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
    	$company = Company::all();
        return view('company.index')->with('company',$company);
    }
    
    public function create()
    {
        return view('company.create');
    }

    public function store(Request $request)
    {
        $this->validate($request,[
			'name'=>'required|max:255',
		]);
		
		$company = new Company;
		$company->name = $request->name;
		$company->contact_person = $request->contact_person;
		$company->mobile = $request->mobile;
		$company->email = $request->email;
		$company->address = $request->address;
		$company->cin = $request->cin;
		$company->gst = $request->gst;
		$company->status = 1;
		$company->user_sys = \Request::ip();
		$company->updated_by = Auth::id();
		$company->created_by = Auth::id();
		
		$result = $company->save();
		
		if($result){
			return back()->with('success', 'Record added successfully!');
		}
		else{
			return back()->with('error', 'Something went wrong!');
		}
    }

    public function show($id)
    {
        $company = Company::find($id);
        return view('company.show')->with('company', $company);
    }

    public function edit($id)
    {
        $company = Company::find($id);
        return view('company.edit')->with('company', $company);
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
		
		$company = Company::find($id);
		$company->name = $request->name;
		$company->contact_person = $request->contact_person;
		$company->mobile = $request->mobile;
		$company->email = $request->email;
		$company->address = $request->address;
		$company->cin = $request->cin;
		$company->gst = $request->gst;
		$company->status = 1;
		$company->user_sys = \Request::ip();
		$company->updated_by = Auth::id();
		
		$result = $company->save();
		
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
			$company = Company::find($id);
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
		$location = Company::where('workshop_id',$location)->get();//->pluck('name','id');
		print_r(json_encode($location));
	}
}
