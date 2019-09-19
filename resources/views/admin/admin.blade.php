<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Admin | Inventory Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
    <!--     Fonts and icons     -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.2/css/all.css" integrity="sha384-/rXc/GQVaYpyDdyxK+ecHPVYJSN9bmVFBvjA/9eOB+pb3F2w2N6fc5qB9Ew5yIns" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,200" rel="stylesheet" type="text/css">
    <!-- Styles -->
    <!--link href="{{ asset('css/app.css') }}" rel="stylesheet"-->
    <link href="{{ asset('css/bootstrap.min.css')}}" rel="stylesheet" />
    <link href="{{ asset('css/now-ui-dashboard.min.css?v=1.2.0')}}" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" media="screen" href="{{asset('css/main.css')}}" />
</head>

<body>
    <div class="wrapper ">
        @include('layouts.admin_sidebar')
        <div class="main-panel">
            @include('layouts.admin_navbar', ['page_title' => 'Dashboard'])
            <div class="panel-header panel-header-lg">
                <canvas id="bigDashboardChart"></canvas>
            </div>
            <div class="content">
                <div class="row center">
                    <div class="col-md-12">
                        <div class="card card-stats">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="statistics">
                                            <div class="info">
                                                <div class="icon icon-info">
                                                    <i class="now-ui-icons health_ambulance"></i>
                                                </div>
                                                <a href="/admin/hospitals"><h6 class="stats-title">Hospitals</h6></a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="statistics">
                                            <div class="info">
                                                <div class="icon icon-info">
                                                    <i class="now-ui-icons health_ambulance"></i>
                                                </div>
                                                <a href="/admin/equipment"><h6 class="stats-title">Equipment</h6></a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="statistics">
                                            <div class="info">
                                                <div class="icon icon-info">
                                                    <i class="now-ui-icons health_ambulance"></i>
                                                </div>
                                                <a href="/admin/donations"><h6 class="stats-title">Donations</h6></a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="statistics">
                                            <div class="info">
                                                <div class="icon icon-info">
                                                    <i class="now-ui-icons health_ambulance"></i>
                                                </div>
                                                <a href="#"><h6 class="stats-title">Reports</h6></a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="statistics">
                                            <div class="info">
                                                <div class="icon icon-info">
                                                    <i class="now-ui-icons location_pin"></i>
                                                </div>
                                                <a href="/admin/districts"><h6 class="stats-title">Districts and Communities</h6></a>
                                            </div>
                                        </div>
                                    </div>
                                    @if(Auth::guard('admin')->user()->role == 'Admin')
                                        <div class="col-md-4">
                                            <div class="statistics">
                                                <div class="info">
                                                    <div class="icon icon-info">
                                                        <i class="now-ui-icons users_single-02"></i>
                                                    </div>
                                                    <a href="/admin/users"><h6 class="stats-title">Users and Engineers</h6></a>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if(Auth::guard('admin')->user()->role == 'Biomedical Engineer')
                                        <div class="col-md-4">
                                            <div class="statistics">
                                                <div class="info">
                                                    <div class="icon icon-info">
                                                        <i class="now-ui-icons users_single-02"></i>
                                                    </div>
                                                    <a href="/admin/assigned"><h6 class="stats-title">Assigned Jobs</h6></a>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('layouts.admin_core_scripts')
    <script src="{{asset('js/chartjs.min.js')}}" type="text/javascript"></script>
    <script>
    $(document).ready(function(){

        data = [50, 150, 100, 190, 130, 90, 150, 160, 120, 140, 190, 95];
        displayGraph(data);

      });

      function displayGraph(data){
        chartColor = "#FFFFFF";
  
        // General configuration for the charts with Line gradientStroke
        gradientChartOptionsConfiguration = {
            maintainAspectRatio: false,
            legend: {
                display: false
            },
            tooltips: {
              bodySpacing: 4,
              mode:"nearest",
              intersect: 0,
              position:"nearest",
              xPadding:10,
              yPadding:10,
              caretPadding:10
            },
            responsive: 1,
            scales: {
                yAxes: [{
                  display:0,
                  gridLines:0,
                  ticks: {
                      display: false
                  },
                  gridLines: {
                      zeroLineColor: "transparent",
                      drawTicks: false,
                      display: false,
                      drawBorder: false
                  }
                }],
                xAxes: [{
                  display:0,
                  gridLines:0,
                  ticks: {
                      display: false
                  },
                  gridLines: {
                    zeroLineColor: "transparent",
                    drawTicks: false,
                    display: false,
                    drawBorder: false
                  }
                }]
            },
            layout:{
              padding:{left:0,right:0,top:15,bottom:15}
            }
        };
  
        var ctx = document.getElementById('bigDashboardChart').getContext("2d");
  
        var gradientStroke = ctx.createLinearGradient(500, 0, 100, 0);
        gradientStroke.addColorStop(0, '#80b6f4');
        gradientStroke.addColorStop(1, chartColor);
  
        var gradientFill = ctx.createLinearGradient(0, 200, 0, 50);
        gradientFill.addColorStop(0, "rgba(128, 182, 244, 0)");
        gradientFill.addColorStop(1, "rgba(255, 255, 255, 0.24)");
  
        var myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ["JAN", "FEB", "MAR", "APR", "MAY", "JUN", "JUL", "AUG", "SEP", "OCT", "NOV", "DEC"],
                datasets: [{
                    label: "Maintenance Frequency",
                    borderColor: chartColor,
                    pointBorderColor: chartColor,
                    pointBackgroundColor: "#1e3d60",
                    pointHoverBackgroundColor: "#1e3d60",
                    pointHoverBorderColor: chartColor,
                    pointBorderWidth: 1,
                    pointHoverRadius: 7,
                    pointHoverBorderWidth: 2,
                    pointRadius: 5,
                    fill: true,
                    backgroundColor: gradientFill,
                    borderWidth: 2,
                    data: data
                }]
            },
            options: {
                title:{
                    display : true,
                    text : 'Cummulative Maintenance Graph',
                    fontStyle : 'normal',
                    fontColor : '#fefefe',
                    fontFamily : 'Montserrat'
                },
                layout: {
                    padding: {
                        left: 20,
                        right: 20,
                        top: 0,
                        bottom: 0
                    }
                },
                maintainAspectRatio: false,
                tooltips: {
                  backgroundColor: '#fff',
                  titleFontColor: '#333',
                  bodyFontColor: '#666',
                  bodySpacing: 4,
                  xPadding: 12,
                  mode: "nearest",
                  intersect: 0,
                  position: "nearest"
                },
                legend: {
                    position: "bottom",
                    fillStyle: "#FFF",
                    display: false
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            fontColor: "rgba(255,255,255,0.4)",
                            fontStyle: "bold",
                            beginAtZero: true,
                            maxTicksLimit: 5,
                            padding: 10
                        },
                        gridLines: {
                            drawTicks: true,
                            drawBorder: false,
                            display: true,
                            color: "rgba(255,255,255,0.1)",
                            zeroLineColor: "transparent"
                        }
  
                    }],
                    xAxes: [{
                        gridLines: {
                            zeroLineColor: "transparent",
                            display: false,
  
                        },
                        ticks: {
                            padding: 10,
                            fontColor: "rgba(255,255,255,0.4)",
                            fontStyle: "bold"
                        }
                    }]
                }
            }
        });
    
      }
      </script>
</body>

</html>