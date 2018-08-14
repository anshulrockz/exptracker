@extends('layouts.app')

@section('content')

<!-- Bootstrap Material Datetime Picker Css -->
<link href="{{ asset('bsb/plugins/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css')}}" rel="stylesheet" />

<!-- Bootstrap Select Css -->
<link href="{{ asset('bsb/plugins/bootstrap-select/css/bootstrap-select.css')}}" rel="stylesheet" />
<link href="{{ asset('bsb/css/datatable-style.css')}}" rel="stylesheet" /> 

<link href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css" rel="stylesheet"/>
<link href="https://cdn.datatables.net/fixedheader/3.1.3/css/fixedHeader.dataTables.min.css" rel="stylesheet"/>

<style type="text/css">
    th{
        text-align: center;
    }
    /*td:nth-child(1), td:nth-child(2){
        text-align: center;
    }*/
    td:nth-child(6), td:nth-child(4), td:nth-child(5){
        text-align: right;
    }
</style>

<div class="row clearfix">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
        	<div class="header">
                <!-- <h1>
                    {{ getFromID(Auth::user()->company_id, 'companies') }}
                    {{ getFromID($vendor->location, 'workshops') }}<br> 
                </h1> -->
                <h1>{{ $vendor->name }}</h1>
                <h2>Address: {{ $vendor->address }}</h2>
                <h2 id="header">Ledger</h2>
                <a class="btn btn-primary waves-effect header-dropdown m-r--5" href="{{ url('payment-vendors/create?id='.$vendor->id)}}">Pay Now</a>
            </div>
            <div class="body">
                <ol class="breadcrumb breadcrumb-bg-pink">
                    <li><a href="{{ url('/dashboard') }}">Home</a></li>
                    <li><a href="{{ url('/payment-vendors') }}">Payments</a></li>
                    <li class="active">{{ $vendor->name }}</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover dataTable">
                        <thead>
                            <tr>
                                <!-- <th>Id</th> -->
                                <th>Date</th>
                                <th>Particulars</th>
                                <th>Debit</th>
                                <th>Credit</th>
                                <th>Balance</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach( $transaction as $key=>$list)
                            <tr>                                
                                <!-- <td>{{$list->id}}</td> -->
                                <td>{{date_format(date_create($list->created_at),"d/m/Y")}}</td>
                                <td>{{$list->particulars}}</td>
                                <td>{{$list->debit}} </td>
                                <td>{{$list->credit}} </td>
                                <td>
                                    {{$balance += $list->debit - $list->credit}}
                                </td>
                                <td>
                                    <form style="display: inline;" method="post" action="{{route('payment-vendors.destroy',$list->id)}}">
                                        {{ csrf_field() }}
                                        {{ method_field('DELETE') }}
                                        <button onclick="return confirm('Are you sure you want to Delete?');" type="submit"class="btn btn-xs btn-danger"
                                        @if($list->txn_type != 2) disabled @endif 
                                        ><i class="material-icons">delete</i></button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <br>
                <div class="row">
                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 right">
                        <div class="info-box-3 bg-red">
                            <div class="icon">
                                <i class="material-icons">&#x20B9;</i>
                            </div>
                            <div class="content">
                                <div class="text">BALANCE</div>
                                <div class="number count-to" data-from="0" data-to="125" data-speed="1000" data-fresh-interval="20"><span>&#x20B9;</span>{{$balance}}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Jquery DataTable Plugin Js -->
<script src="{{ asset('bsb/plugins/jquery-datatable/jquery.dataTables.js') }}"></script>
<script src="https://cdn.datatables.net/fixedheader/3.1.3/js/dataTables.fixedHeader.min.js"></script>
<script src="{{ asset('bsb/plugins/jquery-datatable/skin/bootstrap/js/dataTables.bootstrap.js') }}"></script>
<script src="{{ asset('bsb/plugins/jquery-datatable/extensions/export/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('bsb/plugins/jquery-datatable/extensions/export/buttons.flash.min.js') }}"></script>
<script src="{{ asset('bsb/plugins/jquery-datatable/extensions/export/jszip.min.js') }}"></script>
<script src="{{ asset('bsb/plugins/jquery-datatable/extensions/export/pdfmake.min.js') }}"></script>
<script src="{{ asset('bsb/plugins/jquery-datatable/extensions/export/vfs_fonts.js') }}"></script>
<script src="{{ asset('bsb/plugins/jquery-datatable/extensions/export/buttons.html5.min.js') }}"></script>
<script src="{{ asset('bsb/plugins/jquery-datatable/extensions/export/buttons.print.min.js') }}"></script>

<!-- Select Plugin Js -->
    <script src="{{ asset('bsb/plugins/bootstrap-select/js/bootstrap-select.js')}}"></script>
@php

$company = getAllFromID(Auth::user()->company_id, "companies") ;
$workshop = getAllFromID($vendor->location, "workshops") ;

@endphp
<script>

$(document).ready(function() {
    var header = $("#header").html();
    document.title = header;
    $('.dataTable').DataTable({
        "order": [[ 0, "desc" ]],
        dom: 'Bfrtip',
        responsive: true,
        buttons: [
            {
                extend: 'print',
                title: '',
                exportOptions: {
                    columns: [ 0, 1, 2, 3, 4 ]
                },
                customize: function ( win ) {
                    $(win.document.body)
                        .css( 'font-size', '10pt' )
                        .prepend(
                            '<h2 style="width:100%;text-align:center"><u>{{ $company->name }} </u><br> {{ $company->address }}</h2>' + 
                            '<h2 style="width:100%;text-align:center">{{ $vendor->name }}</h2>' + 
                            '<h3 style="width:100%;text-align:center"> {{ $vendor->address }} </h3>'
                            // {{ $workshop->name }} <br>
                        );
 
                    $(win.document.body).find( 'table' )
                        .addClass( 'compact' )
                        .css( 'font-size', 'inherit' );
                    
                }
            }
        ]
    });

    $('.dataTable').column( 0 ).visible( false );
} );
</script>

@endsection
