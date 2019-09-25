<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Admin | Inventory Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.2/css/all.css" integrity="sha384-/rXc/GQVaYpyDdyxK+ecHPVYJSN9bmVFBvjA/9eOB+pb3F2w2N6fc5qB9Ew5yIns" crossorigin="anonymous">
    <!--     Fonts and icons     -->
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
            @include('layouts.admin_navbar', ['page_title' => 'Engineer Requests'])
            <div class="panel-header panel-header-sm">
            </div>
            <div class="content">
                <div class="row">
                    <div class="col-md-12 center">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="inline-block">Engineer Requests</h4>
                            </div>
                            <div class="card-body">
                                <table id="requests-table" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Description</th>
                                            <th>Priority</th>
                                            <th>Hospital</th>
                                            <th>Assigned to</th>
                                            <th>Status</th>
                                            <th class="text-right">&nbsp;</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>Title</th>
                                            <th>Description</th>
                                            <th>Priority</th>
                                            <th>Hospital</th>
                                            <th>Assigned to</th>
                                            <th>Status</th>
                                            <th class="text-right">&nbsp;</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        @foreach($requests as $request)
                                        <tr>
                                            <td>{{$request->work_order != null ? $request->work_order->title : 'N/A'}}</td>
                                            <td>{{$request->description != null ? $request->description : 'N/A'}}</td>
                                            <td>{{$request->work_order->priority != null ? $request->work_order->priority->name : 'N/A'}}</td>
                                            <td>{{$request->work_order != null ? $request->work_order->hospital->name : 'N/A'}}</td>
                                            <td>{{$request->engineer != null ? $request->engineer->firstname.' '.$request->engineer->lastname : 'N/A'}}</td>
                                            <td>
                                                @if($request->status == 2)
                                                <span class="badge badge-warning">Pending</span>
                                                @elseif($request->status == 1)
                                                <span class="badge badge-success">Accepted</span>
                                                @else
                                                <span class="badge badge-danger">Declined</span>
                                                @endif
                                            </td>
                                            <td class="text-right">
                                                <div class="dropdown">
                                                    <span
                                                        style="cursor:pointer"
                                                        href="javascript:void(0)"
                                                        class="dropdown-toggle"
                                                        data-toggle="dropdown">
                                                        Action
                                                    </span>
                                                    <div class="dropdown-menu dropdown-menu-right">
                                                        @if($request->status == 2 && $request->work_order != null && $request->work_order->status != 1)
                                                            <a class="dropdown-item" href="javascript:void(0)" onclick="approve('{{$request->id}}')">Assign Engineer</a>
                                                            <a class="dropdown-item" href="javascript:void(0)" onclick="decline('{{$request->id}}')">Decline Request</a>
                                                        @elseif($request->status == 1 && $request->work_order != null && $request->work_order->status != 1)
                                                            <a class="dropdown-item" href="javascript:void(0)" onclick="approve('{{$request->id}}')">Change Assignment</a>
                                                        @elseif($request->status == 0 && $request->work_order != null && $request->work_order->status != 1)
                                                            <a class="dropdown-item" href="javascript:void(0)" onclick="revertDecline('{{$request->id}}')">Revert decline</a>
                                                        @else
                                                            <a class="dropdow-item disabled" href="javascript:void(0)" disabled>No actions available</a>
                                                        @endif
                                                    </div>
                                                </div>
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
        </div>
        <div class="modal fade" id="assign_engineer">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;<span class="sr-only">Close</span></button>
                        <h6 class="header">Assign Engineer</h6>
                    </div>
                    <form id="assign_form">
                        <div class="modal-body">
                            <p>Select engineer to assign</p>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-row">
                                        <div>
                                            <select class="selectpicker" data-style="form-control" name="admin_id"
                                             data-live-search="true" data-show-tick="true" title="Select Engineer" style="width:100%;" required>
                                                @foreach($engineers as $engineer)
                                                <option value="{{$engineer->id}}">{{$engineer->firstname.' '.$engineer->lastname}}</option>
                                                @endforeach
                                            </select>
                                            <input type="hidden" name="request_id" id="assign_id"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer mt-4 right">
                            <button type="button" data-dismiss="modal" class="btn btn-light">Cancel</button>
                            <button type="submit" class="btn btn-purple">Assign</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade" id="decline_request">
            <div class="modal-dialog">
            <form id="decline_form">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;<span class="sr-only">Close</span></button>
                        <h6 class="header">Decline Request</h6>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to decline this request?</p>
                        <input type="hidden" name="request_id" id="decline_id"/>
                    </div>
                    <div class="modal-footer mt-4 right">
                        <button type="button" data-dismiss="modal" class="btn btn-light">Cancel</button>
                        <button type="submit" class="btn btn-danger">Decline</button>
                    </div>
                </div>
            </form>
            </div>
        </div>
        <div class="modal fade" id="revert_decline_request">
            <div class="modal-dialog">
            <form id="revert_decline_form">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;<span class="sr-only">Close</span></button>
                        <h6 class="header">Revert Decline</h6>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to revert this request back to pending?</p>
                        <input type="hidden" name="request_id" id="revert_decline_id"/>
                    </div>
                    <div class="modal-footer mt-4 right">
                        <button type="button" data-dismiss="modal" class="btn btn-light">Cancel</button>
                        <button type="submit" class="btn btn-warning">Revert</button>
                    </div>
                </div>
            </form>
            </div>
        </div>
    @include('layouts.admin_core_scripts')
    <script src="{{asset('js/datatables.js')}}"></script>
    <script src="{{asset('js/bootstrap-notify.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/bootstrap-selectpicker.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/moment.min.js')}}"></script>
    <script src="{{asset('js/bootstrap-datetimepicker.js')}}"></script>
    <script>
        $(document).ready(function () {
            $('#requests-table').DataTable({
                "pagingType": "full_numbers",
                "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                "order": [[ 4, "desc" ]],
                responsive: true,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Find a request",
                }
            });
        });

        const approve = (request_id) => {
            $("#assign_id").val(request_id);
            $("#assign_engineer").modal("show");
        }

        const decline = (request_id) => {
            $("#decline_id").val(request_id);
            $("#decline_request").modal("show");
        }

        const revertDecline = (request_id) => {
            $("#revert_decline_id").val(request_id);
            $("#revert_decline_request").modal("show");
        }

        $("#assign_form").on("submit", function(e){
            e.preventDefault();
            const btn = $(this).find('[type=submit]');
            const data = $(this).serialize();

            submit_form("/api/engineer-request/approve", "post", data, undefined, btn, true);
        });

        $("#decline_form").on("submit", function(e){
            e.preventDefault();
            const btn = $(this).find('[type=submit]');
            const id = $("#decline_id").val();

            submit_form(`/api/engineer-request/${id}/decline`, "post", null, undefined, btn, true);
        });

        $("#revert_decline_form").on("submit", function(e){
            e.preventDefault();
            const btn = $(this).find('[type=submit]');
            const id = $("#revert_decline_id").val();

            submit_form(`/api/engineer-request/${id}/revert`, "post", null, undefined, btn, true);
        });
    </script>
</body>

</html>