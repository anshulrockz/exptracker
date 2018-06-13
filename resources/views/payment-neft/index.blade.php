@extends('layouts.app')

@section('content')

<!-- Bootstrap Select Css -->
<link href="{{ asset('bsb/plugins/bootstrap-select/css/bootstrap-select.css')}}" rel="stylesheet" />
<link href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css" rel="stylesheet"/>
<link href="https://cdn.datatables.net/fixedheader/3.1.3/css/fixedHeader.dataTables.min.css" rel="stylesheet"/>

<!-- Bootstrap Material Datetime Picker Css -->
<link href="{{ asset('bsb/plugins/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css')}}" rel="stylesheet" />

    
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
                    <li class="active">Payment</li>
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
                    All
                </h2>
                <a class="btn btn-primary waves-effect header-dropdown m-r--5" href="{{ url('/payment/others/create')}}">Add New</a>
            </div>
            <div class="body">
                <div class="">
                    <table class="table table-bordered table-striped table-hover dataTable">
                        <thead>
                            <tr>
                                <th>Sr No</th>
                                <th>Date</th>
                                <th>Seller Name</th>
                                <th>Amount</th>
                                <th>Booked By</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach( $cheque as $key=>$list)
                            <tr>
                                <td>{{ $list->voucher_no }}</td>
                                <td>{{date_format(date_create($list->created_at),"d/m/Y")}}</td>
                                <td>{{$list->party_name}}</td>
                                <td>
                                   {{round($list->amount,2)}} 
                                </td>
                                
                                <td>{{$list->user}}</td>
                                <td>
                                    @if($list->payment_status==1 || $list->payment_status=='Cash') Recieved
                                    @else Pending
                                        @if(Auth::user()->user_type == 1 || Auth::user()->user_type == 5 && $list->payment_status == 2)
                                        <button type="button" class="btn btn-default btn-xs modal-buttons" data-toggle="modal" data-target="#myModal" data-id="{{$list->id}}"><i class="material-icons">help</i></button>
                                            <!-- <a href="{{ url('/cheques/change-status/'.$list->id)}}" class="btn btn-xs btn-default"> <i class="material-icons">money</i> </a> -->
                                        @endif
                                    @endif
                                </td>
                                <!-- <td>
                                    @if($list->status==1) Active
                                    @else Cancelled
                                    @endif
                                </td> -->
                                <td>
                                    <!-- <a href="{{ url('/cheques/'.$list->id)}}" class="btn btn-xs btn-success"> View </a> -->
                                    @if($list->status==1)
                                    <a href="{{ url('/cheques/'.$list->id.'/edit')}}" class="btn btn-xs btn-info" disabled> <i class="material-icons">edit</i> </a>
                                    
                                    @php
                                        $date1=date_create($list->created_at);
                                        $date2=date_create(date("y-m-d H:i:s"));
                                        $diff=date_diff($date2,$date1);
                                        $days = $diff->format("%a");
                                    @endphp
                                    @if( $days<'1' )
                                    <a href="{{ url('/cheques/cancel/'.$list->id)}}" class="btn btn-xs btn-warning"> <i class="material-icons">cancel</i> </a>
                                    @endif
                                    @endif
                                    @if(Auth::user()->id==1 || Auth::user()->id==5)
                                    <button type="button" class="js-modal-buttons btn btn-default btn-xs  waves-effect " data-id="{{$list->voucher_no}}" data-amount="{{$list->amount}}" data-toggle="modal" title="Deposit To Bank"><i class="material-icons">play_for_work</i></button>
                                    <form style="display: inline;" method="post" action="{{route('others.destroy',$list->id)}}">
                                        {{ csrf_field() }}
                                        {{ method_field('DELETE') }}
                                        <button onclick="return confirm('Are you sure you want to delete?');" type="submit" class="btn btn-xs btn-danger" title="DELETE"><i class="material-icons">delete</i></button>
                                    </form>
                                    <div class="clearfix"></div>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Dialogs ====================================================================================================================== -->
            <!-- Default Size -->
            <div class="modal fade" id="defaultModal" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <form method="post" action="{{url('cheques/payment')}}">
                                {{ csrf_field() }}
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="defaultModalLabel">Payment Details</h4>
                        </div>
                        <div class="modal-body">
                            <label for="voucher_no">Sr No</label>
                            <div class="form-group">
                                <div class="form-line" id="voucher_no_div">
                                    <input type="hidden" id="voucher_no" name="voucher_no" class="form-control" placeholder="Enter asset category name" value="{{ old('voucher_no') }}" >
                                </div>
                            </div>
                            <label for="amount">Amount</label>
                            <div class="form-group">
                                <div class="form-line" id="amount_div">
                                    <input type="hidden" id="amount" name="amount" class="form-control" placeholder="Enter asset category name" value="{{ old('amount') }}" >
                                </div>
                            </div>
                            <label for="bank">Bank</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <select class="form-control show-tick" id="bank" name="bank">
                                        <option value="" >-- Please select category --</option>
                                        @foreach($bank as $list)
                                        <option value="{{$list->id}}">{{$list->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <label for="name">Deposit Date</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" id="date" name="date" class="form-control datepicker" placeholder="Enter date" value="{{ old('date') }}" required>
                                </div>
                            </div>
                            <label for="remark">Remark</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <textarea id="remark" name="remark" rows="1" class="form-control no-resize auto-growth" placeholder="Enter remark(press ENTER for more lines)">{{ old('remark') }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success waves-effect">SAVE</button>
                            <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CANCEL</button>
                        </div>
                    </div>
                    </form>
                </div>
            </div>

<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog modal-sm">
        <form method="post" action="{{url('cheques/change-status')}}">
        {{ csrf_field() }}
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Recieved the payment?</h4>
                </div>
                <div class="modal-body">
                    <div class="form-line" id="id_div"></div>
                    <input name="status" id="radio_1" type="radio" required value="1">
                    <label for="radio_1">Yes</label>
                    <input name="status" id="radio_2" type="radio" value="3">
                    <label for="radio_2">No</label>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Submit</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Jquery DataTable Plugin Js -->
    <script src="{{ asset('bsb/plugins/jquery-datatable/jquery.dataTables.js') }}"></script>
    <script src="https://cdn.datatables.net/fixedheader/3.1.3/js/dataTables.fixedHeader.min.js"></script>

<!-- Select Plugin Js -->
    <script src="{{ asset('bsb/plugins/bootstrap-select/js/bootstrap-select.js')}}"></script>
<!-- Moment Plugin Js -->
<script src="{{ asset('bsb/plugins/momentjs/moment.js')}}"></script>

<!-- Bootstrap Material Datetime Picker Plugin Js -->
<script src="{{ asset('bsb/plugins/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js')}}"></script>

<script>
$('.datepicker').bootstrapMaterialDatePicker({
    format: 'DD MMMM YYYY',
    clearButton: true,
    weekStart: 1,
    time: false
});
</script>

<script>
$(document).ready(function() {
    $('.dataTable').DataTable( {
        "order": [[ 1, "desc" ]],
        fixedHeader: {
            header: true,
            headerOffset: $('#navbar-collapse').height()
        }
    });
});

$(function () {
    $('.js-modal-buttons').on('click', function () {
        var color = $(this).data('color');
        var voucher_no = $(this).data('id');
        var amount = $(this).data('amount');

        $('#defaultModal .modal-content').removeAttr('class').addClass('modal-content modal-col-' + color);
        
        $('#voucher_no_div').html('<input type="text" value="'+voucher_no+'" name="voucher_no" class="form-control" placeholder="Enter asset category name" readonly>');

        $('#amount_div').html('<input type="text" value="'+amount+'" name="amount" class="form-control" placeholder="Enter asset category name" readonly >');

        //$('#voucher_no').val(voucher_no);
        //$('#amount').val(amount);
        
        $('#defaultModal').modal('show');
    });
});

$(function () {
    $('.modal-buttons').on('click', function () {
        var id = $(this).data('id');

        $('#id_div').html('<input type="hidden" value="'+id+'" name="id" class="form-control" placeholder="Enter asset category name">');
    });
});
</script>

@endsection
