@extends('layouts.app')

@section('content')

<!-- Bootstrap Material Datetime Picker Css -->
<link href="{{ asset('bsb/plugins/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css')}}" rel="stylesheet" />

<!-- Bootstrap Select Css -->
<link href="{{ asset('bsb/plugins/bootstrap-select/css/bootstrap-select.css')}}" rel="stylesheet" />

<!-- Dropzone Css -->
<link href="{{ asset('bsb/plugins/dropzone/dropzone.css')}}" rel="stylesheet"/>

<!-- AJAX DD Selecter for Location Js -->

<script>
$(document).ready(function() {
	$('.acc_no').hide();
	$('.ifsc').hide();
	$('.txn_no').hide();
});
function paymentMode(mode){
	if(mode == '3'){
		$('.acc_no').show();
		$('.ifsc').show();
		// $('.txn_no').show();
	}
	else if(mode == '2'){
		// $('.txn_no').show();
		$('.acc_no').hide();
		$('.ifsc').hide();
	}
	else if(mode == '1'){
		// $('.txn_no').hide();
		$('.acc_no').hide();
		$('.ifsc').hide();
	}
	else{
		$('.acc_no').hide();
		$('.ifsc').hide();;
		// $('.txn_no').hide();
	}
}
</script>
<div class="row clearfix">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
        	<div class="header">
                <h2>
                    Payment
                </h2>
            </div>
            <div class="body">
                <ol class="breadcrumb breadcrumb-bg-pink">
                    <li><a href="{{ url('/dashboard') }}">Home</a></li>
                    <li><a href="{{ url('/payment/others') }}">Payment</a></li>
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
                <form method="post" action="{{route('nefts.store')}}" enctype="multipart/form-data">
                	{{ csrf_field() }}
                	<div class="row clearfix pannel-hide">
	                	<div class="col-sm-3">
		                    <label for="voucher_no">Sr No.(auto)</label>
		                    <div class="form-group">
		                        <div class="form-line">
		                            <input type="text" id="voucher_no" name="voucher_no" class="form-control" placeholder="Enter number" value="{{ $voucher_no }}" disabled="">
		                        </div>
		                    </div>
	                    </div>
                	 	<div class="col-sm-3">
		                    <label for="voucher_date">Date</label>
		                    <div class="form-group">
		                        <div class="form-line">
		                            <input type="text" id="voucher_date" name="voucher_date" class="form-control datepicker" placeholder="Enter Date" value="{{  date('d F Y') }}" disabled>
		                        </div>
		                    </div>
	                    </div>
	                    <div class="col-sm-3">
		                    <label for="cheque_no">Ref No.</label>
		                    <div class="form-group">
		                        <div class="form-line focused">
		                            <input type="text" id="cheque_no" name="cheque_no" class="form-control" placeholder="Enter cheque No" value="{{ old('cheque_no') }}" >
		                        </div>
		                    </div>
	                    </div>
	                    <div class="col-sm-3">
		                    <label for="cheque_date">Date</label>
		                    <div class="form-group">
		                        <div class="form-line">
		                            <input type="text" id="cheque_date" name="cheque_date" class="form-control datepicker" placeholder="Enter Date Of cheque" value="{{ old('cheque_date') }}" >
		                        </div>
		                    </div>
	                    </div>
	                    <div class="col-sm-6 ">
		                    <label for="amount">Amount</label>
		                    <div class="form-group">
		                        <div class="form-line">
		                            <input type="text" id="amount" name="amount" class="form-control" placeholder="Enter amount" value="{{ old('amount') }}" >
		                        </div>
		                    </div>
	                    </div>
	                    <div class="col-sm-6 ">
		                    <label for="party_name">Party Name</label>
		                    <div class="form-group">
		                        <div class="form-line">
		                            <input type="text" id="party_name" name="party_name" class="form-control" placeholder="Enter Party name" value="{{ old('party_name') }}" >
		                        </div>
		                    </div>
	                    </div>
	                </div>
	                <div class="row clearfix">
	                	<div class="col-sm-6 ">
		                    <label for="bank">Bank</label>
		                    <div class="form-group">
		                        <div class="form-line">
		                            <input type="text" id="bank" name="bank" class="form-control" placeholder="Enter bank name" value="{{ old('bank') }}" >
		                        </div>
		                    </div>
	                    </div>
	                    <div class="col-sm-6 ">
		                    <label for="location">Location</label>
		                    <div class="form-group">
			                    <div class="form-line">
			                        <select class="form-control show-tick" id="location" name="location" @if(Auth::user()->user_type == 3 || Auth::user()->user_type == 4) disabled @endif>
			                            <option >select</option>
			                            @foreach($workshop as $list)
			                            <option value="{{$list->id}}" @if(Auth::user()->workshop_id == $list->id) selected @endif >{{$list->name}}</option>
			                            @endforeach
			                        </select>
		                    	</div>
	                    	</div>
	                    </div>
	                    <div class="col-sm-6 acc_no">
		                    <label for="acc_no">Account No</label>
		                    <div class="form-group">
		                        <div class="form-line">
		                            <input type="text" id="acc_no" name="acc_no" class="form-control" placeholder="Enter account no" value="{{ old('acc_no') }}">
		                        </div>
		                    </div>
	                    </div>
	                    <div class="col-sm-6 ifsc">
		                    <label for="ifsc">IFSC</label>
		                    <div class="form-group">
		                        <div class="form-line">
		                            <input type="text" id="ifsc" name="ifsc" class="form-control" placeholder="Enter IFSC of bank acc" value="{{ old('ifsc') }}">
		                        </div>
		                    </div>
	                    </div>
	                    <div class="col-sm-6 txn_no">
		                    <label for="txn_no">Transaction No.</label>
		                    <div class="form-group">
		                        <div class="form-line">
		                            <input type="text" id="txn_no" name="txn_no" class="form-control" placeholder="Enter Payment ID" value="{{ old('txn_no') }}">
		                        </div>
		                    </div>
	                    </div>
	                </div>
	                <div class="row clearfix">
	                    <div class="col-sm-6 ">
		                    <label for="voucher_img">Upload Document</label>
		                    <div class="form-group">
		                        <div class="form-line">
	                                <div class="fallback">
	                                    <input name="voucher_img" id="voucher_img" class="form-control" type="file" placeholder="img only" accept="image/x-png,image/gif,image/jpeg" />
	                                </div>
			                    </div>
		                    </div>
	                    </div>
	                    <!-- <div class="col-sm-6">
		                    <label for="remarks">Remark</label>
		                    <div class="form-group">
		                        <div class="form-line">
		                            <textarea id="remarks" name="remarks" rows="1" class="form-control no-resize auto-growth" placeholder="Remarks if any... (press ENTER for more lines)">{{ old('remarks') }}</textarea>
		                        </div>
		                    </div>
	                    </div> -->
	                </div>
	                <div class="row clearfix pannel-hide">
	                	<div class="col-md-12 col-sm-12 pull-right">
	                		<button type="submit" class="btn btn-success waves-effect">Save</button>
	                	</div>
	                </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Moment Plugin Js -->
<script src="{{ asset('bsb/plugins/momentjs/moment.js')}}"></script>

<!-- Bootstrap Material Datetime Picker Plugin Js -->
<script src="{{ asset('bsb/plugins/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js')}}"></script>

<!-- Dropzone Plugin Js -->
<script src="{{ asset('bsb/plugins/dropzone/dropzone.js')}}"></script>

<script>
	$('.datepicker').bootstrapMaterialDatePicker({
        format: 'DD MMMM YYYY',
        clearButton: true,
        weekStart: 1,
        time: false
    });
    
    Dropzone.options.frmFileUpload = {
        paramName: "file",
        maxFilesize: 2
    };
</script>
    
<!-- Select Plugin Js -> -->
<!-- <script src="{{ asset('bsb/plugins/bootstrap-select/js/bootstrap-select.js')}}"></script> -->

@endsection
