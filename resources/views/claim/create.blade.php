@extends('layouts.claim')

@section('content')

<div class="row clearfix">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
        	<div class="header">
                <h2>
                    Claim
                </h2>
            </div>
            <div class="body">
                <ol class="breadcrumb breadcrumb-bg-pink">
                    <li><a href="{{ url('/dashboard') }}">Home</a></li>
                    <li><a href="{{ url('/claim') }}">Claim</a></li>
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
                <form method="post" action="{{route('claim.store')}}">
                	{{ csrf_field() }}
                    <div class="row">
                        <div class="col-md-6">
                            <label for="name">Name of Insurer</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" id="name" name="name" class="form-control" placeholder="Enter company name" value="{{ old('name') }}" >
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="acc_no">Name of Insured</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="number" id="acc_no" name="acc_no" class="form-control" placeholder="Enter account number" value="{{ old('acc_no') }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="bank">Contact</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" id="bank" name="bank" class="form-control" placeholder="Enter bank name" value="{{ old('bank') }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="ifsc">Email</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" id="ifsc" name="ifsc" class="form-control" placeholder="Enter ifsc " value="{{ old('ifsc') }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="code">Vehicle No.</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" id="code" name="code" class="form-control" placeholder="Enter code" value="{{ old('code') }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="code">Vehicle Type</label>
                            <div class="form-group">
                                <select class="form-control show-tick" id="name" name="name" required>
                                    <option value="">-- Please select --</option>
                                </select>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-md-6">
                            <label for="code">Make</label>
                            <div class="form-group">
                                <select class="form-control show-tick" id="name" name="name" required>
                                    <option value="">-- Please select --</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="code">Model</label>
                            <div class="form-group">
                                <select class="form-control show-tick" id="name" name="name" required>
                                    <option value="">-- Please select --</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="code">Date Of Loss</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" id="code" name="code" class="form-control" placeholder="Enter code" value="{{ old('code') }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="code">Date of Bill Received</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" id="code" name="code" class="form-control" placeholder="Enter code" value="{{ old('code') }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="code">Date of Survey</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" id="code" name="code" class="form-control" placeholder="Enter code" value="{{ old('code') }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="code">Date of Reinspection</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" id="code" name="code" class="form-control" placeholder="Enter code" value="{{ old('code') }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="code">Place of Survey</label>
                            <div class="form-group">
                                <select class="form-control show-tick" id="name" name="name" required>
                                    <option value="">-- Please select --</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="code">Workshop</label>
                            <div class="form-group">
                                <select class="form-control show-tick" id="name" name="name" required>
                                    <option value="">-- Please select --</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="code">Machenic Contact No.</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" id="code" name="code" class="form-control" placeholder="Enter code" value="{{ old('code') }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="code">Machenic Email</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" id="code" name="code" class="form-control" placeholder="Enter code" value="{{ old('code') }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="code">Sum Of Insued Amount</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" id="code" name="code" class="form-control" placeholder="Enter code" value="{{ old('code') }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="code">Estimated Loss Amount</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" id="code" name="code" class="form-control" placeholder="Enter code" value="{{ old('code') }}">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <h2>
                                Document Verification
                            </h2>
                        </div>
                        <div class="col-md-4">
                            <label for="code">Document</label>
                        </div>
                        <div class="col-md-4">
                            <label for="code">Recieved</label>
                        </div>
                        <div class="col-md-4">
                            <label for="code">Upload Document</label>
                        </div>

                        <div class="col-md-4">
                            <label for="code">Claim Form</label>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <select class="form-control show-tick" id="name" name="name" required>
                                    <option value="">-- Please select --</option>
                                    <option value="1">Yes</option>
                                    <option value="2">No</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="file" id="code" name="code" class="form-control" placeholder="Enter code" value="{{ old('code') }}">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label for="code">Driving Licence</label>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <select class="form-control show-tick" id="name" name="name" required>
                                    <option value="">-- Please select --</option>
                                    <option value="1">Yes</option>
                                    <option value="2">No</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="file" id="code" name="code" class="form-control" placeholder="Enter code" value="{{ old('code') }}">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label for="code">RC</label>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <select class="form-control show-tick" id="name" name="name" required>
                                    <option value="">-- Please select --</option>
                                    <option value="1">Yes</option>
                                    <option value="2">No</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="file" id="code" name="code" class="form-control" placeholder="Enter code" value="{{ old('code') }}">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label for="code">Fitness</label>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <select class="form-control show-tick" id="name" name="name" required>
                                    <option value="">-- Please select --</option>
                                    <option value="1">Yes</option>
                                    <option value="2">No</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="file" id="code" name="code" class="form-control" placeholder="Enter code" value="{{ old('code') }}">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label for="code">Road Tax</label>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <select class="form-control show-tick" id="name" name="name" required>
                                    <option value="">-- Please select --</option>
                                    <option value="1">Yes</option>
                                    <option value="2">No</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="file" id="code" name="code" class="form-control" placeholder="Enter code" value="{{ old('code') }}">
                                </div>
                            </div>
                        </div>


                        <div class="col-md-12">
                            <label for="address">Branch Address</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <textarea id="address" name="address" class="form-control no-resize auto-growth" placeholder="Enter address(press ENTER for more lines)">{{ old('address') }}</textarea>
                                </div>
                            </div>
                        </div>
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
