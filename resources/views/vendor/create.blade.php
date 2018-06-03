@extends('layouts.app')

@section('content')

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
                    <li class="active">Create</li>
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
                <form method="post" action="{{route('vendors.store')}}">
                	{{ csrf_field() }}
                    <div class="row">
                        <div class="col-md-6">
                            <label for="name">Vendor Name</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" id="name" name="name" class="form-control" placeholder="Enter name" value="{{ old('name') }}" >
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="gst">GST</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" id="gst" name="gst" class="form-control" placeholder="Enter gst " value="{{ old('gst') }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="state">State</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" id="state" name="state" class="form-control" placeholder="Enter state name" value="{{ old('state') }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="state_code">State Code</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" id="state_code" name="state_code" class="form-control" placeholder="Enter state code" value="{{ old('state_code') }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="mobile">Phone Number</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" id="mobile" name="mobile" class="form-control" placeholder="Enter mobile name" value="{{ old('mobile') }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="address">Address</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <textarea id="address" name="address" rows="1" class="form-control no-resize auto-growth" placeholder="Enter address(press ENTER for more lines)">{{ old('address') }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-md-6">
                            <label for="bank_name">Vendor Bank</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" id="bank_name" name="bank_name" class="form-control" placeholder="Enter bank name" value="{{ old('bank_name') }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="acc_no">Account Number</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" id="acc_no" name="acc_no" class="form-control" placeholder="Enter account number " value="{{ old('acc_no') }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="ifsc">IFSC</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" id="ifsc" name="ifsc" class="form-control" placeholder="Enter ifsc " value="{{ old('ifsc') }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="branch">Branch Address</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <textarea id="branch" name="branch" rows="1" class="form-control no-resize auto-growth" placeholder="Enter branch address(press ENTER for more lines)">{{ old('branch') }}</textarea>
                                </div>
                            </div>
                        </div>
                        <!-- <div class="col-md-6">
                            <label for="remark">Remark</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <textarea id="remark" name="remark" rows="1" class="form-control no-resize auto-growth" placeholder="Enter remark(press ENTER for more lines)">{{ old('remark') }}</textarea>
                                </div>
                            </div>
                        </div> -->
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-success waves-effect">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
