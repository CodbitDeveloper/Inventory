<!DOCTYPE html>
<html>

<head>
<meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Inventory Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport'
    />
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Fonts -->
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,200" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.2/css/all.css" integrity="sha384-/rXc/GQVaYpyDdyxK+ecHPVYJSN9bmVFBvjA/9eOB+pb3F2w2N6fc5qB9Ew5yIns" crossorigin="anonymous">
    <!-- Styles -->
    <!--link href="{{ asset('css/app.css') }}" rel="stylesheet"-->
    <link href="{{ asset('css/bootstrap.min.css')}}" rel="stylesheet" />
    <link href="{{ asset('css/now-ui-dashboard.min.css?v=1.2.0')}}" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" media="screen" href="{{asset('css/main.css')}}" />
    
    <style>
        .text-small{
            font-size: 12px;
        }

        .badge-dark{
            background-color: #777777;
            color: #ffffff;
        }
    </style>
</head>

<body>
    <div class="wrapper">
    @include('layouts.admin_sidebar')
        <div class="main-panel">
            @include('layouts.admin_navbar', ['page_title' => 'Biomedical Engineers'])
            <div class="panel-header panel-header-sm">
            </div>
            <div class="content">
                <div class="row">
                    <div class="col-md-12 center">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="inline-block">Work Orders</h4>
                            </div>
                            <div class="card-body">
                                <!--add filter tree here-->
                                <div class="row">
                                    <div class="col-md-12">
                                        <table id="works" class="table table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Title</th>
                                                    <th>#</th>
                                                    <th>Due Date</th>
                                                    <th>Status</th>
                                                    <th>Priority</th>
                                                    <th>Equip.</th>
                                                    <th>Last Updated</th>
                                                    <th>Created</th>
                                                </tr>
                                            </thead>
                                            <tfoot>
                                                <tr>
                                                    <th>Title</th>
                                                    <th>#</th>
                                                    <th>Due Date</th>
                                                    <th>Status</th>
                                                    <th>Priority</th>
                                                    <th>Equip.</th>
                                                    <th>Last Updated</th>
                                                    <th>Created</th>
                                                </tr>
                                            </tfoot>
                                            <tbody>
                                            @foreach($work_orders as $work_order)
                                                <tr>
                                                    <td><a href="/admin/work-order/{{$work_order->id}}"><b>{{$work_order->title}}</b></a></td>
                                                    <td>{{$work_order->wo_number}}</td>
                                                    <td>{{$work_order->due_date != null ? date('jS F, Y', strtotime($work_order->due_date)) : 'N/A'}}</td>
                                                    <td>
                                                        @if($work_order->status == 1)
                                                        <span class="badge badge-dark">Closed</span>
                                                        @elseif($work_order->status == 2)
                                                        <span class="badge badge-success">In Progress</span>
                                                        @elseif($work_order->status == 3)
                                                        <span class="badge badge-primary">On Hold</span>
                                                        @elseif($work_order->status == 4)
                                                        <span class="badge badge-info">Open</span>
                                                        @elseif($work_order->status == 5)
                                                        <span class="badge badge-warning">Pending</span>
                                                        @endif
                                                    </td>
                                                    <td>{{$work_order->priority != null ? $work_order->priority->name : 'N/A'}}</td>
                                                    </td>
                                                    <td>{{$work_order->asset != null ? $work_order->asset->name : 'N/A'}}</td>
                                                    <td>{{Carbon\Carbon::parse($work_order->updated_at)->format('jS F, Y')}}</td>
                                                    <td>{{Carbon\Carbon::parse($work_order->created_at)->format('jS F, Y')}}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    
    <!--   Core JS Files   -->
    <!--script src="{{ asset('js/app.js') }}" defer></script-->
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{asset('js/popper.min.js')}}"></script>
    <script src="{{asset('js/bootstrap.min.js')}}"></script>
    <script src="{{asset('js/perfect-scrollbar.jquery.min.js')}}"></script>
    <script src="{{asset('js/main.js')}}"></script>
    <script src="{{asset('js/now-ui-dashboard.min.js?v=1.2.0')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatables.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/bootstrap-selectpicker.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/bootstrap-notify.js')}}" type="text/javascript"></script>
    <script>
        $(document).ready(function () {
            $(function () {
                $('[data-toggle="tooltip"]').tooltip()
            });

            $('#datatable').DataTable({
                "pagingType": "full_numbers",
                "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                responsive: true,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Find an item",
                }
            });

            var table = $('#works').DataTable();
        });

        /*$('#active').on('switchChange.bootstrapSwitch', function(e, s){
            var form_data = $('toggle_active').serialize();
            alert(form_data);

            request = $.ajax({
                url : '/api/users/activate',

            })
        });*/

        function setActive(admin)
        {
            var active = admin.active;
            var id = admin.id;

            if(active == 0){
                active = 1;
            }else{
                active = 0;
            }

            var form_data = "admin_id="+id+"&active="+active;

            $.ajax({
                url : '/api/admins/activate',
                method : 'put',
                data : form_data,
                success: function(data, status){
                    if(data.error){
                        presentNotification(data.message, 'danger', 'top', 'right');
                    }else{
                        presentNotification(data.message, 'info', 'top', 'right');
                    }
                },

                 error: function(xhr, desc, err){
                     presentNotification('Network error', 'danger', 'top', 'right');
                 }
            });
        }
    
    </script>
</body>

</html>