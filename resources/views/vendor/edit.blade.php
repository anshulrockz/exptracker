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
                    Vendor
                </h2>
            </div>
            <div class="body">
                <ol class="breadcrumb breadcrumb-bg-pink">
                    <li><a href="{{ url('/dashboard') }}">Home</a></li>
                    <li><a href="{{ url('/vendors') }}">Vendor</a></li>
                    <li><!-- <a href="{{ url('/vendors/'.$vendor->id) }}"> -->{{$vendor->name}}</a></li>
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
                <form id="form" method="post" action="{{route('vendors.update',$vendor->id)}}">
                	{{ csrf_field() }}
	                {{ method_field('PUT') }}
	                <div class="row">
                        <div class="col-md-6">
                            <label for="name">Vendor Name</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" id="name" name="name" class="form-control" placeholder="Enter name" value="{{ $vendor->name }}" >
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="gst">GST</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" id="gst" name="gst" class="form-control" placeholder="Enter gst " value="{{ $vendor->gst }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="state">State</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" id="state" name="state" class="form-control" placeholder="Enter state name" value="{{ $vendor->state }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="state_code">State Code</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" id="state_code" name="state_code" class="form-control" placeholder="Enter state code" value="{{ $vendor->state_code }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="mobile">Phone Number</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" id="mobile" name="mobile" class="form-control" placeholder="Enter mobile name" value="{{ $vendor->mobile }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="address">Address</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <textarea id="address" name="address" rows="1" class="form-control no-resize auto-growth" placeholder="Enter address(press ENTER for more lines)">{{ $vendor->address }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-md-6">
                            <label for="bank_name">Vendor Bank</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" id="bank_name" name="bank_name" class="form-control" placeholder="Enter bank name" value="{{ $vendor->bank_name }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="acc_no">Account Number</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" id="acc_no" name="acc_no" class="form-control" placeholder="Enter account number " value="{{ $vendor->acc_no }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="ifsc">IFSC</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" id="ifsc" name="ifsc" class="form-control" placeholder="Enter ifsc " value="{{ $vendor->ifsc }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="branch">Branch Address</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <textarea id="branch" name="branch" rows="1" class="form-control no-resize auto-growth" placeholder="Enter branch address(press ENTER for more lines)">{{ $vendor->branch }}</textarea>
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
