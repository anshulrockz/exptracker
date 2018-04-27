<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Workshop;
use App\Company;
use App\User;
use Auth;

class WorkshopController extends Controller
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
        if(Auth::user()->user_type==1)
    	{
	        $workshop = Workshop::AllWorkshops();
	        return view('workshop.index')->with('workshop',$workshop);
		}
		
		if(Auth::user()->user_type > 1)
    	{
			$workshop = Workshop::find( Auth::user()->workshop_id);
	        return view('workshop.show')->with('workshop',$workshop);
		}
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $company = Company::all();
        return view('workshop.create')->with('company',$company);
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
			'company'=>'required|max:255',
			'name'=>'required|max:255',
			'code'=>'required|max:255',
		]);
		
		$workshop = new Workshop;
		$workshop->company = $request->company;
		$workshop->name = $request->name;
		$workshop->code = $request->code;
		$workshop->reg_no = $request->reg_no;
		$workshop->mobile = $request->mobile;
		$workshop->phone = $request->phone;
		$workshop->email = $request->email;
		$workshop->address = $request->address;
		$workshop->gst = $request->gst;
		if ($request->location_type == "other") {
			$workshop->location_type = $request->other;
		}
		else $workshop->location_type = $request->location_type;
		$workshop->user_sys = \Request::ip();
		$workshop->updated_by = Auth::id();
		$workshop->created_by = Auth::id();
		
		$result = $workshop->save();
		
		if($result){
			return back()->with('success', 'Record added successfully!');
		}
		else{
			return back()->with('error', 'Something went wrong!');
		}
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    	$workshop = Workshop::find($id);
        return view('workshop.show')->with('workshop',$workshop);
    }

    public function edit($id)
    {
    	$company = Company::all();
        $workshop = Workshop::find($id);
        return view('workshop.edit')->with( array('workshop' => $workshop,'company' => $company ));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request,[
			'company'=>'required|max:255',
			'name'=>'required|max:255',
			'code'=>'required|max:255',
		]);
		
		$workshop = Workshop::find($id);
		$workshop->company = $request->company;
		$workshop->name = $request->name;
		$workshop->code = $request->code;
		$workshop->reg_no = $request->reg_no;
		$workshop->mobile = $request->mobile;
		$workshop->phone = $request->phone;
		$workshop->email = $request->email;
		$workshop->address = $request->address;
		$workshop->gst = $request->gst;
		if ($request->location_type == "other") {
			$workshop->location_type = $request->other;
		}
		else $workshop->location_type = $request->location_type;
		$workshop->user_sys = \Request::ip();
		$workshop->updated_by = Auth::id();
		$workshop->created_by = Auth::id();
		
		$result = $workshop->save();
		
		if($result){
			return back()->with('success', 'Record added successfully!');
		}
		else{
			return back()->with('error', 'Something went wrong!');
		}
    }

    public function destroy($id)
    {
        $count = User::all()->where('workshop_id',$id)->count();
        
        if($count==0){
			$workshop = Workshop::find($id);
        	$result = $workshop->delete($id);
		
	        if($result){
				return redirect()->back()->with('success', 'Record deleted successfully!');
			}
			else{
				return redirect()->back()->with('error', 'Something went wrong!');
			}
		}
		else{
				return redirect()->back()->with('error', 'You cannot delete the workshop because users exist in workshop');
			}
    }
    
    public function id_ajax(Request $request)
    {
		$id = $request->id;
		$workshop = Workshop::where('company',$id)->get();//->pluck('name','id');
		print_r(json_encode($workshop));
	}
}
