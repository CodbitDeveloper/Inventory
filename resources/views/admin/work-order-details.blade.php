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
            @include('layouts.admin_navbar', ['page_title' => 'Edit Hospital'])
            <div class="panel-header panel-header-sm">
            </div>
            <div class="content">
                <div class="row">
                    <div class="col-md-9 col-sm-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="title">{{$work_order->title}}</h5>
                                <ul class="nav nav-tabs nav-tabs-primary text-center">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-toggle="tab" role="tablist" href="#details">Details</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" role="tablist" href="#activity">Activity</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" role="tablist" href="#parts">Spare Parts</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content tab-space">
                                    <div class="tab-pane active" id="details">
                                        <div class="row mb-4" style="margin-top: -50px">
                                            <div class="col-md-12 text-right">
                                                <label><b>Status</b></label>
                                                @if($work_order->status == 1)
                                                <span class="badge badge-dark">Closed</span>
                                                @if($work_order->is_complete == 1)
                                                    &nbsp;&nbsp;<i data-toggle="tooltip" title="Marked as complete" class="fas fa-check-circle text-success"></i>
                                                @endif 
                                                @elseif($work_order->status == 2)
                                                <span class="badge badge-success">In Progress</span>
                                                @elseif($work_order->status == 3)
                                                <span class="badge badge-primary">On Hold</span>
                                                @elseif($work_order->status == 4)
                                                <span class="badge badge-info">Open</span>
                                                @elseif($work_order->status == 5)
                                                <span class="badge badge-warning">Pending</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="row mb-4">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <h6>Description</h6>
                                                    <p class="text-muted">{{$work_order->description != null ? $work_order->description : 'No description provided'}}</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-4">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label><b>Date Created</b></label>
                                                    <p>{{Carbon\Carbon::parse($work_order->created_at)->format('jS F, Y')}}</p>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label><b>Due Date</b></label>
                                                    <p>{{$work_order->due_date != null ? Carbon\Carbon::parse($work_order->due_date)->format('jS F, Y') : 'N/A'}}</p>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <h6>Lead Technician</h6>
                                                    <p>{{$work_order->user == null ? 'None Assigned' : $work_order->user->firstname.' '.$work_order->user->lastname}}</p>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label><b>Fault Category</b></label>
                                                    <p>{{$work_order->fault_category == null ? 'N/A' : $work_order->fault_category->name}}</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-4">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <h6>Additional Technicians</h6>
                                                    @if($work_order->users->count() == 0)
                                                    <span class="text-muted">None assigned</span>&nbsp;
                                                    @else
                                                    @foreach($work_order->users as $team_user)
                                                    <img class="round" width="30" height="30" avatar="{{$team_user->firstname.' '.$team_user->lastname}}" data-toggle="tooltip" title="{{$team_user->firstname.' '.$team_user->lastname}}"/>
                                                    @endforeach
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-4">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <h6><b>Equipment</b></h6>
                                                    <span>
                                                    @if($work_order->asset != null)
                                                        {{$work_order->asset->name}}
                                                    @else
                                                    N/A <span class="add-asset text-primary" data-toggle="modal" data-target="#assign_asset">Assign asset</span>
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="activity">
                                        <a href="javascript:void(0)" class="btn btn-round pull-right" 
                                        data-toggle="modal" data-target="#add_activity" style="margin-top: -50px" @if($work_order->is_complete == 1) disabled @endif>Add Activity</a>
                                        <div class="row">
                                            <div class="col-sm-12" id="activities">
                                                <p class="text-center"><i class="now-ui-icons arrows-1_refresh-69 spin"></i></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="parts">
                                        <table id="spare-parts" class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Part Name</th>
                                                    <th>Quantity</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tfoot>
                                                <tr>
                                                    <th>Part Name</th>
                                                    <th>Quantity</th>
                                                    <th>Action</th>
                                                </tr>
                                            </tfoot>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-12">
                        <div class="card card-comments">
                            <div class="card-header">
                                <h6 class="title">Comments</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div id="comments" class="col-md-12">
                                        <p class="text-center"><i class="now-ui-icons arrows-1_refresh-69 spin"></i></p>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <form id="add_comment">
                                    <textarea class="form-control" placeholder="Write comment" name="comment" id="comment"></textarea>
                                    <button type="submit" class="btn btn-round btn-purple mt-2 pull-right mb-3"  @if($work_order->is_complete == 1) disabled @endif>Comment</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="add_activity">
        <div class="modal-dialog">
            <form method="post" id="add_activity_form">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;<span class="sr-only">Close</span></button>
                        <h6 class="header">Record Activity</h6>
                    </div>
                    <div class="modal-body">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label><b>What work was done?</b></label>
                                <textarea class="form-control" name="activity"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer mt-4 right">
                        <button type="submit" class="btn btn-purple">Record</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @include('layouts.admin_core_scripts')
    <script src="{{asset('js/bootstrap-notify.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/bootstrap-selectpicker.js')}}" type="text/javascript"></script>@section('scripts')
    <script src="{{asset('js/moment.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/bootstrap-datetimepicker.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatables.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/bootstrap-selectpicker.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/bootstrap-notify.js')}}" type="text/javascript"></script>
    <script>
    $("[data-toggle='tooltip']").tooltip();
    let parts_table = generateDtbl("#spare-parts", "No parts associated with this work order", "Search parts");

    $(document).ready(function(){
        fetchActivities();
        fetchComments();
        fetchParts();
        demo.initDateTimePicker();
    });
    
    const fetchActivities = () => {
        $.ajax({
            url : '/api/work-order/{{$work_order->id}}/activities',
            data : 'GET',
            success : (data) => {
                if(data.length == 0){
                    $("#activities").html(null);
                    $("#activities").append(`<p class="text-muted text-bold text-center">No activities recorded for this work order</p>`)
                }else{
                    const colors = ["primary", "danger", "info", "warning"];
                    $("#activities").html(`
                    <div class="card card-timeline card-plain">
                            <div class="card-body">
                                <ul class="timeline">

                                </ul>
                            </div>
                        </div>
                    `);

                    $.each(data, function(index, activity){
                        const swatch = colors[Math.floor(Math.random() * colors.length)];
                        let css_class = "";
                        if(index%2 == 0){
                            css_class = "timeline-inverted";
                        }
                        $(".timeline").append(`
                            <li class="${css_class}">
                                <div class="timeline-badge ${swatch}">
                                    <i class="now-ui-icons ui-1_settings-gear-63"></i>
                                </div>
                                <div class="timeline-panel">
                                    <div class="timeline-heading">
                                        <span class="badge badge-${swatch}">${activity.pivot.created_at}</span>
                                    </div>
                                    <div class="timeline-body">
                                        <p>${activity.pivot.action_taken}</p>
                                        <p class="text-small text-right"><i>recorded by <a href="javascript:void(0)">${activity.firstname} ${activity.lastname}</a></i></p>
                                    </div>
                                </div>
                            </li>
                        `);
                    })
                }
            },
            error : (xhr) => {
                console.log(xhr);
            }
        });
    }

    const fetchParts = () => {
        $.ajax({
            url : '/api/work-order/{{$work_order->id}}/spare-parts',
            data : 'GET',
            success : (data) => {
                if(data.length > 0){
                    let parts = [];
                    $.each(data, function(index, part){
                        let temp = [part.name, part.pivot.quantity, @if($work_order->is_complete == 1) `N/A` @else `<a href="javascript:void(0)" class="text-12 text-info">Edit</a>&nbsp;&nbsp;<a href="javascript:void(0)" class="text-12 text-danger">Remove</a>`@endif];
                        parts.push(temp);
                    });
                    parts_table.rows.add(parts).draw();
                }
            },
            error : (xhr) => {
            }
        });
    }

    const fetchComments = () => {
        $.ajax({
            url : '/api/work-order/{{$work_order->id}}/comments',
            data : 'GET',
            success : (data) => {
                if(data.length == 0){
                    $("#comments").html(`<p><i><b>No comments made</b></i></p>`)
                }else{
                    $('#comments').html(null);
                    $.each(data, function(index, comment){
                        $('#comments').append(`
                        <div class="col-md-12">
                            <p>${comment.comment}<br/>
                            <span class="text-small"><a href="javascript:void(0)">${comment.user.firstname} ${comment.user.lastname}</a> <i>${comment.created_at}</i></span>
                            </p>
                        </div>
                        `)
                    });
                }
            },
            error : (xhr) => {
            }
        });
    }
    
    $("#add_activity_form").on("submit", function(e){
        e.preventDefault();
        let data = new FormData(this);
        data.append("user_id", "{{$admin->id}}");
        data.append("type", "admin");
        let btn = $(this).find('[type="submit"]');

        submit_file_form("/api/work-order/{{$work_order->id}}/record-activity", "post", data, undefined, btn, true);
    });

    $("#add_comment").on("submit", function(e){
        e.preventDefault();
        let data = new FormData(this);
        data.append("user_id", "{{$admin->id}}");
        data.append("type", "admin");
        let btn = $(this).find('[type="submit"]');
        
        const success = (data) => {
            if($('#comments').html() == `<p><i><b>No comments made</b></i></p>`){
                $('#comments').html(null);
            }

            $("#comments").append(`<div class="col-md-12">
                <p>${data.comment.comment}<br/>
                <span class="text-small">
                <a href="javascript:void(0)">{{$admin->firstname.' '.$admin->lastname}}</a> <i>${data.comment.created_at}</i></span>
                </p>
            </div>`);
            $("#comment").html(null);
        }
        submit_file_form("/api/work-order/{{$work_order->id}}/comment", "post", data, success, btn, false);
    });
    
    </script>
</body>

</html>