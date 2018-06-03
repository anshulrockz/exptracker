@extends('layouts.app')

@section('content')

<script>
    $(function(){
        $("#workshop").change(function(){
            var id = $(this).val();
            if(id == '0'){
                window.location.href = '{{url('dashboard')}}'
            }
            else if(id != ''){
                window.location.href = '{{url('dashboard')}}'+'?location='+id;
                //location.reload();
            }
        });
    })
</script>

<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="header">
                <h2>
                    DASHBOARD
                </h2>
                @if(Auth::user()->user_type == 1)
                <div class="header-dropdown m-r--5">
                    <div class=" col-md-3 form-line-label">
                        <label  for="workshop">Location</label>
                    </div>
                    <div class="col-md-9 m-t--10">
                        <div class="form-group">
                            <div class="form-line">
                                <select class="form-control" id="workshop" name="workshop">
                                    <option value="0">All</option>
                                    @foreach($workshops as $key=>$value)
                                    <option value="{{$value->id}}" 
                                        @if(isset($_GET['location']))
                                        @if($_GET['location'] == $value->id) selected @endif 
                                        @endif >{{$value->name}}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="header">
                <h2>Deposits</h2>
            </div>
            <div class="body">
                <canvas id="bar_chart" height="150"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="header">
                <h2>Expenses</h2>
            </div>
            <div class="body">
                <canvas id="bar_chart2" height="150"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row clearfix">
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        <div class="card">
            <div class="header">
                <h2>Expense</h2>
            </div>
            <div class="body">
                <canvas id="pie_chart1" height="150"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        <div class="card">
            <div class="header">
                <h2>Asset</h2>
            </div>
            <div class="body">
                <canvas id="pie_chart2" height="150"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row clearfix">
    <!-- <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        <div class="card">
            <div class="header">
                <h2>
                    Balance Remaining
                </h2>
            </div>
            <div class="body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover js-basic-example dataTable">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Balance</th>
                                <th>Last Expense date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div> -->
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        <div class="card">
            <div class="header">
                <h2>
                    Deposits
                </h2>
            </div>
            <div class="body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover js-basic-example dataTable">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Amount</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($deposits_2 as $key => $value)
                            <tr>
                                <td>{{ $value->user }}</td>
                                <td>{{ $value->amount }}</td>
                                <td>{{date_format(date_create($value->date),"d/m/Y")}}</td>
                            </tr>
                            @endforeach 
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
$(function () {
    new Chart(document.getElementById("bar_chart").getContext("2d"), getChartJs('bar'));
    new Chart(document.getElementById("bar_chart2").getContext("2d"), getChartJs('bar2'));
    new Chart(document.getElementById("pie_chart1").getContext("2d"), getChartJs('pie1'));
    new Chart(document.getElementById("pie_chart2").getContext("2d"), getChartJs('pie2'));
});
function getChartJs(type) {
    var config = null;
    if (type === 'bar') {
        config = {
            type: 'bar',
            data: {
                labels: [
                    @foreach($deposits as $key => $value)
                        @if($value->m ==1 ) "January",
                        @elseif($value->m ==2 ) "February",
                        @elseif($value->m ==3 ) "March",
                        @elseif($value->m ==4 ) "April", 
                        @elseif($value->m ==5 ) "May", 
                        @elseif($value->m ==6 ) "June", 
                        @elseif($value->m ==7 ) "July", 
                        @elseif($value->m ==8 ) "August", 
                        @elseif($value->m ==9 ) "September", 
                        @elseif($value->m ==10) "October", 
                        @elseif($value->m ==11) "November", 
                        @elseif($value->m ==12) "December"
                        @endif
                    @endforeach
                     	@if($last_month ==1) "January",
                        @elseif($last_month ==2 ) "February",
                        @elseif($last_month ==3 ) "March",
                        @elseif($last_month ==4 ) "April", 
                        @elseif($last_month ==5 ) "May", 
                        @elseif($last_month ==6 ) "June", 
                        @elseif($last_month ==7 ) "July", 
                        @elseif($last_month ==8 ) "August", 
                        @elseif($last_month ==9 ) "September", 
                        @elseif($last_month ==10) "October", 
                        @elseif($last_month ==11) "November", 
                        @elseif($last_month ==12) "December"
                        @endif
                ],
                datasets: [{
                    label: "Total Deposits",
                    data: [
                    @foreach($deposits as $key => $value)
                        {{ $value->total }},
                    @endforeach
                    0,
                    ],
                    backgroundColor: 'rgba(0, 188, 212, 0.7)'
                }]
            },
            options: {
                responsive: true,
                legend: false
            }
        }
    }

    if (type === 'bar2') {
        config = {
            type: 'bar',
            data: {
                labels: [
                    @foreach($expenses as $key => $value)
                        @if($value->m ==1 ) "January",
                        @elseif($value->m ==2 ) "February",
                        @elseif($value->m ==3 ) "March",
                        @elseif($value->m ==4 ) "April", 
                        @elseif($value->m ==5 ) "May", 
                        @elseif($value->m ==6 ) "June", 
                        @elseif($value->m ==7 ) "July", 
                        @elseif($value->m ==8 ) "August", 
                        @elseif($value->m ==9 ) "September", 
                        @elseif($value->m ==10) "October", 
                        @elseif($value->m ==11) "November", 
                        @elseif($value->m ==12) "December"
                        @endif
                     @endforeach 
                     	@if($last_month_e ==1 || $last_month_e ==13) "January",
                        @elseif($last_month_e ==2 ) "February",
                        @elseif($last_month_e ==3 ) "March",
                        @elseif($last_month_e ==4 ) "April", 
                        @elseif($last_month_e ==5 ) "May", 
                        @elseif($last_month_e ==6 ) "June", 
                        @elseif($last_month_e ==7 ) "July", 
                        @elseif($last_month_e ==8 ) "August", 
                        @elseif($last_month_e ==9 ) "September", 
                        @elseif($last_month_e ==10) "October", 
                        @elseif($last_month_e ==11) "November", 
                        @elseif($last_month_e ==12) "December"
                        @endif
                ],
                datasets: [{
                    label: "Total Expenses",
                    data: [
                    @foreach($expenses as $key => $value)
                        {{ $value->cost+$value->sgst+$value->cgst+$value->igst }},
                    @endforeach 
                    	0,
                    ],
                    backgroundColor: 'rgba(233, 30, 99, 0.7)'
                }]
            },
            options: {
                responsive: true,
                legend: false
            }
        }
    }
    else if (type === 'pie1') {
        config = {
            type: 'pie',
            data: {
                labels: [
                    @foreach($expense_pc as $key => $value)
                        "{{ $value->category3 }} {{ round((($value->cost+$value->sgst+$value->cgst+$value->igst)*100)/$total_e,2) }}%" ,
                    @endforeach
                ],
                datasets: [{
                    data: [
                            @foreach($expense_pc as $key => $value)
                              {{ $value->cost+$value->sgst+$value->cgst+$value->igst }},
                            @endforeach 
                            ],
                    backgroundColor: [
                        "rgb(233, 30, 99)",
                        "rgb(255, 193, 7)",
                        "rgb(0, 188, 212)",
                        "rgb(0, 200, 212)",
                        "rgb(0, 188, 200)",
                        "rgb(200, 188, 212)",
                        "rgb(0, 150, 212)",
                        "rgb(0, 188, 150)",
                        "rgb(150, 195, 74)"
                    ],
                }],
                
            },
            options: {
                responsive: true,
                legend: false
            }
        }
    }
    else if (type === 'pie2') {
        config = {
            type: 'pie',
            data: {
                datasets: [{
                    data: [
                            @foreach($asset_old as $key => $value)
                                {{ $value->total }},
                            @endforeach 
                            @foreach($asset_new as $key => $value)
                                {{ $value->total }},
                            @endforeach 
                        ],
                    backgroundColor: [
                        "rgb(233, 30, 99)",
                        "rgb(255, 193, 7)",
                        "rgb(0, 188, 212)",
                        "rgb(139, 195, 74)",
                        "rgb(0, 195, 74)",
                        "rgb(139, 0, 74)",
                        "rgb(139, 195, 0)",
                        "rgb(150, 195, 74)",
                        "rgb(139, 200, 74)",
                        "rgb(139, 195, 100)",
                    ],
                }],
                labels: [
                    @foreach($asset_old as $key => $value)
                        "Old {{ $value->main_category }} {{ round(($value->total*100)/$total_a,2) }}%" ,
                    @endforeach
                    @foreach($asset_new as $key => $value)
                        "{{ $value->main_category }} {{ round(($value->total*100)/$total_a,2) }}%" ,
                    @endforeach 
                ]
            },
            options: {
                responsive: true,
                legend: false
            }
        }
    }
    return config;
}

</script>

@endsection
