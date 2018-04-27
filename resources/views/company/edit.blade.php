@extends('layouts.app')

@section('content')

<script>
$( document ).ready(function() {
    $("#form input").prop("disabled", true);
    $("#form select").prop("disabled", true);
    $("#form textarea").prop("disabled", true);
    $("#form-save").prop("disabled", true);
});

$(function() {
    $("#form-edit").click(function() {
     	$("#form input").prop("disabled", false);
     	$("#form select").prop("disabled", false);
    	$("#form textarea").prop("disabled", false);
    	$("#form-save").prop("disabled", false);
    });
});
</script>

<!-- JQuery DataTable Css -->
<link href="{{ asset('bsb/plugins/jquery-datatable/skin/bootstrap/css/dataTables.bootstrap.css') }}" rel="stylesheet"/>
    
<div class="row clearfix">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
        	<div class="header">
                <h2>
                    Companies
                </h2>
            </div>
            <div class="body">
                <ol class="breadcrumb breadcrumb-bg-pink">
                    <li><a href="{{ url('/dashboard') }}">Home</a></li>
                    <li><a href="{{ url('/companies') }}">Companies</a></li>
                    <li><a href="{{ url('/companies/'.$company->id) }}">{{$company->name}}</a></li>
                    <li class="active">Edit</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row clearfix">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		@include('layouts.flashmessage')
	</div>
</div>

<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="header">
                <h2>
                    Details
                </h2>
            </div>
            <div class="body">
                <form id="form" method="post" action="{{route('companies.update',$company->id)}}">
                	{{ csrf_field() }}
	                {{ method_field('PUT') }}
	                <div class="row clearfix">
		                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
		                    <label for="name">Name</label>
		                    <div class="form-group">
		                        <div class="form-line">
		                            <input type="text" id="name" name="name" class="form-control" placeholder="Enter company name" value="{{ $company->name }}" >
		                        </div>
		                    </div>
		                </div>
		                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
		                    <label for="contact_person">Contact Person</label>
		                    <div class="form-group">
		                        <div class="form-line">
		                            <input type="text" id="contact_person" name="contact_person" class="form-control" placeholder="Enter contact person name" value="{{ $company->contact_person }}">
		                        </div>
		                    </div>
		                </div>
		                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
		                    <label for="mobile">Mobile</label>
		                    <div class="form-group">
		                        <div class="form-line">
		                            <input type="text" id="mobile" name="mobile" class="form-control" placeholder="Enter mobile number" value="{{ $company->mobile }}">
		                        </div>
		                    </div>
		                </div>
		                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
		                    <label for="email">Email</label>
		                    <div class="form-group">
		                        <div class="form-line">
		                            <input type="text" id="email" name="email" class="form-control" placeholder="Enter email id" value="{{ $company->email }}">
		                        </div>
		                    </div>
		                </div>
		                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
		                    <label for="cin">Corporate Identity Number</label>
		                    <div class="form-group">
		                        <div class="form-line">
		                            <input type="text" id="cin" name="cin" class="form-control" placeholder="Enter company CIN number" value="{{ $company->cin }}">
		                        </div>
		                    </div>
		                </div>
		                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
		                    <label for="gst">GST Number</label>
		                    <div class="form-group">
		                        <div class="form-line">
		                            <input type="text" id="gst" name="gst" class="form-control" placeholder="Enter company GST number" value="{{ $company->gst }}">
		                        </div>
		                    </div>
		                </div>
		                 <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		                    <label for="address">Address</label>
		                    <div class="form-group">
		                        <div class="form-line">
		                            <textarea id="address" name="address" rows="1" class="form-control no-resize auto-growth" placeholder="Enter address of workshop (press ENTER for more lines)">{{ $company->address }}</textarea>
		                        </div>
		                    </div>
		                </div>
		            </div>
	                <div class="row clearfix">
	                	<div class="col-sm-6">
	                		<button type="submit" id="form-save" class="btn btn-primary waves-effect">Save</button>
	                		<button type="button" id="form-edit" class="btn btn-primary waves-effect">Edit</button>
	                	</div>
	                </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
