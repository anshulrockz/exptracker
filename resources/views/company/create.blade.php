@extends('layouts.app')

@section('content')

<div class="row clearfix">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
        	<div class="header">
                <h2>
                    Company
                </h2>
            </div>
            <div class="body">
                <ol class="breadcrumb breadcrumb-bg-pink">
                    <li><a href="{{ url('/dashboard') }}">Home</a></li>
                    <li><a href="{{ url('/companies') }}">Company</a></li>
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
                <form method="post" action="{{route('companies.store')}}">
                	{{ csrf_field() }}
                    <label for="name">Name</label>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="text" id="name" name="name" class="form-control" placeholder="Enter company name" value="{{ old('name') }}" >
                        </div>
                    </div>
                    <label for="contact_person">Contact Person</label>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="text" id="contact_person" name="contact_person" class="form-control" placeholder="Enter contact person name" value="{{ old('contact_person') }}">
                        </div>
                    </div>
                    <label for="mobile">Mobile</label>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="text" id="mobile" name="mobile" class="form-control" placeholder="Enter mobile number" value="{{ old('mobile') }}">
                        </div>
                    </div>
                    <!--<label for="phone">Phone</label>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="text" id="phone" name="phone" class="form-control" placeholder="Enter phone number" value="{{ old('phone') }}">
                        </div>
                    </div>-->
                    <label for="email">Email</label>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="text" id="email" name="email" class="form-control" placeholder="Enter email id" value="{{ old('email') }}">
                        </div>
                    </div>
                    <label for="address">Address</label>
                    <div class="form-group">
                        <div class="form-line">
                            <textarea id="address" name="address" rows="1" class="form-control no-resize auto-growth" placeholder="Enter address of workshop (press ENTER for more lines)">{{ old('address') }}</textarea>
                        </div>
                    </div>
                    <label for="cin">Corporate Identity Number</label>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="text" id="cin" name="cin" class="form-control" placeholder="Enter company CIN number" value="{{ old('cin') }}">
                        </div>
                    </div>
                    <label for="gst">GST Number</label>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="text" id="gst" name="gst" class="form-control" placeholder="Enter company GST number" value="{{ old('gst') }}">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary m-t-15 waves-effect">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
