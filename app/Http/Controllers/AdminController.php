<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Admin;
use App\User;
use App\EmployeeType;
use App\Workshop;
use App\Designation;
use App\Department;
use Auth;

class AdminController extends Controller
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
    	$users = User::all()->where('company_id',Auth::user()->company_id);
        return view('admin.index')->with('users',$users);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    	$employee_types = EmployeeType::all();
    	$workshops = Workshop::all()->where('company',Auth::user()->company_id);
    	$designations = Designation::all();
    	$hods = Department::all();
        return view('user.create')->with(array('workshops' => $workshops, 'designations' => $designations, 'employee_types' => $employee_types, 'hods' => $hods,));
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
			'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
			'mobile'=>'required|max:255',
			'workshop'=>'required|max:255',
			'location'=>'required|max:255',
			'employee_type'=>'required|max:255'
		]);
		
		$user = new User;
		
		if(empty($request->hod))
		$user->hod = 0;
		else
		$user->hod = $request->hod;
		
		$date = $request->dob;
		$user->dob = date_format(date_create($date),"Y-m-d");
		
		$user->name = $request->name;
		$user->email = $request->email;
		$user->password = bcrypt($request->password);
		$user->mobile = $request->mobile;
		$user->phone = $request->phone;
		$user->company_id = Auth::user()->company_id;
		$user->workshop_id = $request->workshop;
		$user->location_id = $request->location;
		$user->department_id = $request->department;
		$user->user_type = $request->employee_type;
		$user->designation = $request->designation;
		$user->address = $request->address;
		$user->status = 1;
		$user->user_sys = 1;
		$user->updated_by = Auth::id();
		$user->created_by = Auth::id();
		$result = $user->save();
		
		
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
    	$users = User::find($id);
    	$employeetype = User::find($id)->EmployeeType;
    	$workshop = User::find($id)->Workshop;
    	$designation = User::find($id)->Designation;
    	$department = User::find($id)->Department;
        return view('user.show')->with(array('users' => $users, 'employeetype' => $employeetype, 'workshop' => $workshop, 'designation' => $designation,'department' => $department));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $users = User::find($id);
        $employee_types = EmployeeType::all();
    	$workshops = Workshop::all();
    	$designations = Designation::all();
    	$hods = Department::all();
        return view('user.edit')->with(array('users' => $users,'workshops' => $workshops, 'designations' => $designations, 'employee_types' => $employee_types, 'hods' => $hods,));
        return view('user.edit')->with('users',$users);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request,[
			'name' => 'required|string|max:255',
            'password' => 'required|string|min:6|confirmed',
			'mobile'=>'required|max:255',
			'workshop'=>'required|max:255',
			'location'=>'required|max:255',
			'employee_type'=>'required|max:255'
		]);
		
		$user = User::find($id);
		
		if(empty($request->hod))
		$user->hod = 0;
		else
		$user->hod = $request->hod;
		
		$date = $request->dob;
		$user->dob = date_format(date_create($date),"Y-m-d");
		
		$user->name = $request->name;
		$user->email = $request->email;
		$user->password = bcrypt($request->password);
		$user->mobile = $request->mobile;
		$user->phone = $request->phone;
		$user->workshop_id = $request->workshop;
		$user->location_id = $request->location;
		$user->department_id = $request->department;
		$user->user_type = $request->employee_type;
		$user->designation = $request->designation;
		$user->address = $request->address;
		$user->status = 1;
		$user->user_sys = 1;
		$user->updated_by = Auth::id();
		$result = $user->save();
		
		
		if($result){
			return back()->with('success', 'Record added successfully!');
		}
		else{
			return back()->with('error', 'Something went wrong!');
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
        $users = User::find($id);
        $result = $users->delete($id);
        if($result){
			return redirect()->back()->with('success', 'Record deleted successfully!');
		}
		else{
			return redirect()->back()->with('error', 'Something went wrong!');
		}
    }
    
    public function id_ajax(Request $request)
    {
		$employee_id = $request->id;
		$employee = User::where('location_id',$employee_id)->get();
		print_r(json_encode($employee));
	}
}
