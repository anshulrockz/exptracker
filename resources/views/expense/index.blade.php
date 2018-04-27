@extends('layouts.app')

@section('content')

<!-- Bootstrap Select Css -->
<link href="{{ asset('bsb/plugins/bootstrap-select/css/bootstrap-select.css')}}" rel="stylesheet" />
<link href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css" rel="stylesheet"/>
<link href="https://cdn.datatables.net/fixedheader/3.1.3/css/fixedHeader.dataTables.min.css" rel="stylesheet"/>

    
<div class="row clearfix">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
        	<div class="header">
                <h2>
                    Expense
                </h2>
            </div>
            <div class="body">
                <ol class="breadcrumb breadcrumb-bg-pink">
                    <li><a href="{{ url('/dashboard') }}">Home</a></li>
                    <li class="active">Expense</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="header">
                <h2>
                    All
                </h2>
                @if(Auth::user()->user_type!=1)
                <a class="btn btn-primary waves-effect header-dropdown m-r--5" href="{{ url('/expenses/create')}}">Add New</a>
                @endif

            </div>
            <div class="body">
                <div class="">
                    <table class="table table-bordered table-striped table-hover dataTable">
                        <thead>
                            <tr>
                                <th>Voucher No</th>
                                <th>Voucher Date</th>
                                <th>Seller Name</th>
                                <th>Amount</th>
                                <th>Payment</th>
                                <th>Booked By</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        	@foreach( $expense as $key=>$list)
                            <tr>
                            	<td id="{{ $list->voucher_no }}">{{ $list->voucher_no }}</td>
                                <td>{{date_format(date_create($list->created_at),"d/m/Y")}}</td>
                                <td>{{$list->party_name}}</td>
                                <td>{{$list->amount}}</td>
                                <td>
                                    @if($list->mode==1 || $list->mode=='Cash') Paid
                                    @else Unpaid
                                    <a href="{{ url('expenses/paid/'.$list->id)}}" class="btn btn-xs btn-warning"> <i class="material-icons">attach_money</i> </a>
                                    @endif
                                </td>
                                <td>{{$list->user}}</td>
                                <td>
                                	@if($list->status==1) Active
                                	@else Cancelled
                                	@endif
                                </td>
                                <td>
                                    <!-- <a href="{{ url('/expenses/'.$list->id)}}" class="btn btn-xs btn-success"> View </a> -->
                                    @if($list->status==1)
                                    <a href="{{ url('/expenses/'.$list->id.'/edit')}}" class="btn btn-xs btn-info"> <i class="material-icons">edit</i> </a>
                                    
                                     @php
	                		$date1=date_create($list->created_at);
							$date2=date_create(date("y-m-d H:i:s"));
							$diff=date_diff($date2,$date1);
							$days = $diff->format("%a");
	                		@endphp
	                		@if($days<1) 
                                    <a href="{{ url('/expenses/cancel/'.$list->id)}}" class="btn btn-xs btn-warning"> <i class="material-icons">cancel</i> </a>
                                    @endif
                                    @endif
                                    @if(Auth::id()==1)
                                    <form style="display: inline;" method="post" action="{{route('expenses.destroy',$list->id)}}">
				                        {{ csrf_field() }}
				                        {{ method_field('DELETE') }}
				                        <button onclick="return confirm('Are you sure you want to delete?');" type="submit" class="btn btn-xs btn-danger" title="Cancel"><i class="material-icons">delete</i></button>
				                    </form>
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

<!-- Jquery DataTable Plugin Js -->
<script src="{{ asset('bsb/plugins/jquery-datatable/jquery.dataTables.js') }}"></script>
<script src="https://cdn.datatables.net/fixedheader/3.1.3/js/dataTables.fixedHeader.min.js"></script>

<!-- Select Plugin Js -->
    <script src="{{ asset('bsb/plugins/bootstrap-select/js/bootstrap-select.js')}}"></script>

<script>
$(document).ready(function() {
    $('.dataTable').DataTable( {
        "order": [[ 1, "desc" ]],
        fixedHeader: {
            header: true,
            headerOffset: $('#navbar-collapse').height()
        }
    } );
} );
</script>

@endsection
