@extends('admin.master')
@section('title', $page_header)
@section('content-header', $page_header)
@section('content')
@push('style')
<style>
    @import "https://code.highcharts.com/css/highcharts.css";

.highcharts-figure,
.highcharts-data-table table {
  min-width: 310px;
  max-width: 800px;
  margin: 1em auto;
}

.highcharts-data-table table {
  font-family: Verdana, sans-serif;
  border-collapse: collapse;
  border: 1px solid #ebebeb;
  margin: 10px auto;
  text-align: center;
  width: 100%;
  max-width: 500px;
}

.highcharts-data-table caption {
  padding: 1em 0;
  font-size: 1.2em;
  color: #555;
}

.highcharts-data-table th {
  font-weight: 600;
  padding: 0.5em;
}

.highcharts-data-table td,
.highcharts-data-table th,
.highcharts-data-table caption {
  padding: 0.5em;
}

.highcharts-data-table thead tr,
.highcharts-data-table tr:nth-child(even) {
  background: #f8f8f8;
}

.highcharts-data-table tr:hover {
  background: #f1f7ff;
}

.highcharts-yaxis .highcharts-axis-line {
  stroke-width: 2px;
}

/* Link the series colors to axis colors */
.highcharts-color-0 {
  fill: #7cb5ec;
  stroke: #7cb5ec;
}

.highcharts-axis.highcharts-color-0 .highcharts-axis-line {
  stroke: #7cb5ec;
}

.highcharts-axis.highcharts-color-0 text {
  fill: #7cb5ec;
}

.highcharts-color-1 {
  fill: #90ed7d;
  stroke: #90ed7d;
}

.highcharts-axis.highcharts-color-1 .highcharts-axis-line {
  stroke: #90ed7d;
}

.highcharts-axis.highcharts-color-1 text {
  fill: #90ed7d;
}
</style>
@endpush
<div class="card">
	<div class="card-header">{{ $page_header }}</div>
	<div class="card-body">
	    <h4>Welcome to Dashboard</h4>
	    <div class="row">
	        <div class="col-md-12">
                <figure class="highcharts-figure">
                  <div id="container"></div>
                </figure>  
	        </div>
	    </div>
	</div>
</div>
@push('script')
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
<script>
    Highcharts.chart('container', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Car Booking Report'
        },
        subtitle: {
            text: 'Four Wheel Nepal'
        },
        xAxis: {
            type: 'category',
            labels: {
                rotation: -45,
                style: {
                    fontSize: '13px',
                    fontFamily: 'Verdana, sans-serif'
                }
            }
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Booking Count'
            }
        },
        legend: {
            enabled: false
        },
        tooltip: {
            pointFormat: 'Booking Count: <b>{point.y:.1f}</b>'
        },
        series: [{
            name: 'Population',
            // data: [
            //     ['Shanghai', 24.2],
            //     ['Beijing', 20.8],
            //     ['Karachi', 14.9],
            //     ['Shenzhen', 13.7],
            //     ['Guangzhou', 13.1],
            //     ['Istanbul', 12.7],
            //     ['Mumbai', 12.4],
            //     ['Moscow', 12.2],
            //     ['São Paulo', 12.0],
            //     ['Delhi', 11.7],
            //     ['Kinshasa', 11.5],
            //     ['Tianjin', 11.2],
            //     ['Lahore', 11.1],
            //     ['Jakarta', 10.6],
            //     ['Dongguan', 10.6],
            //     ['Lagos', 10.6],
            //     ['Bengaluru', 10.3],
            //     ['Seoul', 9.8],
            //     ['Foshan', 9.3],
            //     ['Tokyo', 9.3]
            // ],
            data : {!! $product_chart_data !!},
            dataLabels: {
                enabled: true,
                rotation: -90,
                color: '#FFFFFF',
                align: 'right',
                format: '{point.y:.1f}', // one decimal
                y: 10, // 10 pixels down from the top
                style: {
                    fontSize: '13px',
                    fontFamily: 'Verdana, sans-serif'
                }
            }
        }]
    });
</script>
@endpush
@endsection