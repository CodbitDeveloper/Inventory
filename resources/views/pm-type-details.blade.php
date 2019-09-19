
@extends('layouts.user-dashboard', ['page_title' => $pmSchedule->title])
@section('styles')
<style>
.text-small{
    font-size:12px;
}

.image-preview{
    cursor:pointer;
}
</style>
@endsection
@section('content')
<div class="content">
    <div class="row">
        <div class="col-md-12 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="title">{{$pmSchedule->title}}</h5>
                    <div class="dropdown" style="position:absolute; top: 0; right: 0;">
                        <button type="button"
                            class="btn btn-round btn-default dropdown-toggle btn-simple"
                            data-toggle="dropdown" style="border:none;">
                            Actions
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <button class="dropdown-item">No actions available</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2">
                            <ul class="nav nav-pills nav-pills-primary nav-pills-icons flex-column" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-toggle="tab" href="#details" role="tablist">
                                        <i class="fas fa-info"></i>
                                        Details
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#history" role="tablist">
                                        <i class="fas fa-chart-bar"></i>
                                        History
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#edit" role="tablist">
                                        <i class="fas fa-edit"></i>
                                        Edit
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#tasks" role="tablist">
                                        <i class="fas fa-list"></i>
                                        Tasks
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-10">
                            <div class="tab-content tab-space">
                                <div class="tab-pane active" id="details">
                                    <div class="row mb-2">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label><b>Title</b></label>
                                                <p>{{$pmSchedule->title}}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label><b>Recurrence</b></label>
                                                <p>{{ucwords($pmSchedule->recurringSchedule)}}</p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label><b>Associated Category</b></label>
                                                <p>{{$pmSchedule->asset_category != null ? $pmSchedule->asset_category->name : 'N/A'}}</p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label><b>Next Due Date</b></label>
                                                <p>{{$pmSchedule->due_date != null ? date('jS F, Y', strtotime($pmSchedule->due_date)) : 'N/A'}}</p>
                                            </div>
                                        </div>
                                    </div>                                   
                                    <div class="row mb-2">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label><b>Priority</b></label>
                                                <p>{{$pmSchedule->priority->name}}</p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label><b>Department</b></label>
                                                <p>{{$pmSchedule->department!= null ? $pmSchedule->department->name : 'N/A'}}</p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label><b>Unit</b></label>
                                                <p>{{$pmSchedule->unit != null ? $pmSchedule->unit->name : 'N/A'}}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label><b>Ends on</b></label>
                                                <p>{{$pmSchedule->endDueDate != null ? date('jS F, Y', strtotime($pmSchedule->endDueDate)) : 'N/A'}}</p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label><b>Reschedule based on completion</b></label>
                                                <p>{{$pmSchedule->rescheduleBasedOnCompletion == 1 ? 'Yes' : 'No'}}</p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label><b>Description</b></label>
                                                <p>{{$pmSchedule->description != null ? $pmSchedule->description : 'N/A'}}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                            <label><b>Associated assets</b></label>
                                            <ul>
                                                @if($pmSchedule->category != null)
                                                <li>All <a href="javascript:void(0)">{{$category->name}}</a> equipment</li>
                                                @endif
                                                @foreach($pmSchedule->assets as $asset)
                                                <li><a href="/inventory/{{$asset->id}}">{{$asset->name}}</a></li>
                                                @endforeach
                                            </ul>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label><b>Tasks</b></label>
                                                <ul>
                                                    @foreach($pmSchedule->actions as $action)
                                                    <li>{{$action->name}}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="history">
                                    <h5 class="title">Work History</h5>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <table id="history_table" class="table table-bordered table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Completed</th>
                                                        <th>Reported</th>
                                                        <th>Reporter</th>
                                                        <th>Actions</th>
                                                        <th>Observations</th>
                                                        <th>Recommendations</th>
                                                        <th>Status</th>
                                                        <th class="disabled-sorting">&nbsp;</th>
                                                    </tr>
                                                </thead>
                                                <tfoot>
                                                    <tr>
                                                        <th>Date Completed</th>
                                                        <th>Date Reported</th>
                                                        <th>Reported By</th>
                                                        <th>Actions</th>
                                                        <th>Observations</th>
                                                        <th>Recommendations</th>
                                                        <th>Status</th>
                                                        <th class="disabled-sorting">&nbsp;</th>
                                                    </tr>
                                                </tfoot>
                                                <tbody>
                                                    @foreach($pmSchedule->preventive_maintenances as $maintenance)
                                                    <tr>
                                                        <td>{{$maintenance->date_completed != null ? date('jS F, Y', strtotime($maintenance->date_completed)) : 'N/A'}}</td>
                                                        <td>{{$maintenance->created_at != null ? date('jS F, Y', strtotime($maintenance->created_at)) : 'N/A'}}</td>
                                                        <td>{{$maintenance->user != null ? $maintenance->user->firstname.' '.$maintenance->user->lastname : 'N/A'}}</td>
                                                        <td>{{$maintenance->action_taken != null ? $maintenance->action_taken : 'N/A'}}</td>
                                                        <td>{{$maintenance->observation != null ? $maintenance->observation : 'N/A'}}</td>
                                                        <td>{{$maintenance->recommendation != null ? $maintenance->recommendation : 'N/A'}}</td>
                                                        <td>
                                                            @if($maintenance->is_completed == 2)
                                                            <span class="badge badge-warning">Pending</span>
                                                            @elseif($maintenance->is_completed == 1)
                                                            <span class="badge badge-success">Approved</span>
                                                            @elseif($maintenance->is_completed == 0)
                                                            <span class="badge badge-danger">Declined</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <div class="dropdown">
                                                                <button type="button"
                                                                    class="btn btn-round btn-default dropdown-toggle btn-simple btn-icon no-caret"
                                                                    data-toggle="dropdown">
                                                                    <i class="fas fa-cog"></i>
                                                                </button>
                                                                <div class="dropdown-menu dropdown-menu-right">
                                                                    @if($maintenance->is_completed == 2)
                                                                    <button class="dropdown-item" onclick="approve('{{$maintenance->id}}')">Mark as <b>approved</b></button>
                                                                    <button class="dropdown-item" onclick="decline('{{$maintenance->id}}')">Mark as <b>declined</b></button>
                                                                    @else
                                                                    <button class="dropdown-item" disabled>No actions available</button>
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
                                <div class="tab-pane" id="edit">
                                    <form id="edit-pm">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="row mb-2">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label><b>Title</b></label>
                                                        <input name="title" value="{{$pmSchedule->title}}" class="form-control"/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mb-2">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label><b>Recurrence</b></label>
                                                        <select class="selectpicker col-sm-12" data-style="form-control" title="Recurring frequency"
                                                        data-show-tick="true" name="recurringSchedule" required>
                                                            <option  <?php if($pmSchedule->recurringSchedule =='daily'){echo 'selected';}?> value="daily" >Daily</option>
                                                            <option <?php if($pmSchedule->recurringSchedule =='weekly'){echo 'selected';}?> value="weekly">Weekly</option>
                                                            <option <?php if($pmSchedule->recurringSchedule =='bi-weekly'){echo 'selected';}?> value="bi-weekly">Bi-weekly</option>
                                                            <option <?php if($pmSchedule->recurringSchedule =='monthly'){echo 'selected';}?> value="monthly">Monthly</option>
                                                            <option <?php if($pmSchedule->recurringSchedule =='bi-monthly'){echo 'selected';}?> value="bi-monthly">Bi-monthly</option>
                                                            <option <?php if($pmSchedule->recurringSchedule =='quarterly'){echo 'selected';}?> value="quarterly">Quarterly</option>
                                                            <option <?php if($pmSchedule->recurringSchedule =='triannually'){echo 'selected';}?> value="triannually">Triannually</option>
                                                            <option <?php if($pmSchedule->recurringSchedule =='biannually'){echo 'selected';}?> value="biannually">Biannually</option>
                                                            <option <?php if($pmSchedule->recurringSchedule =='yearly'){echo 'selected';}?> value="yearly">Yearly</option>
                                                            <option <?php if($pmSchedule->recurringSchedule =='biennially'){echo 'selected';}?> value="biennially">Biennially</option>
                                                        </select>
                                                        <p class="refresh-picker text-right pr-4">Reset</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label><b>Next Due Date</b></label>
                                                        <input name="due_date" class="form-control datetimepicker" value="{{$pmSchedule->due_date != null ? date('m/d/Y H:i:s', strtotime($pmSchedule->due_date)) : null}}" />
                                                    </div>
                                                </div>
                                            </div>                                   
                                            <div class="row mb-2">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label><b>Priority</b></label>
                                                        <select class="selectpicker col-sm-12" data-style="form-control" title="Priority"
                                                        data-show-tick="true" name="priority_id">
                                                            @foreach($hospital->priorities as $priority)
                                                            <option  <?php if($priority->id == $pmSchedule->priority_id){echo 'selected';}?>  value="{{$priority->id}}">{{$priority->name}}</option>
                                                            @endforeach
                                                        </select>
                                                        <p class="refresh-picker text-right pr-4">Reset</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label><b>Department</b></label>
                                                        <select class="selectpicker col-md-12" data-style="btn form-control" name="department_id" title="Department" id="department" data-show-tick="true">
                                                        @if($hospital->departments->count() > 0)
                                                            @foreach($hospital->departments as $department)
                                                            <option value="{{$department->id}}" data-units = "{{$department->units}}"  <?php if($department->id == $pmSchedule->department_id){echo 'selected';}?> >{{$department->name}}</option>
                                                            @endforeach
                                                        @else
                                                            <option disabled>No known departments</option>
                                                        @endif
                                                        </select>
                                                        <p class="refresh-picker pr-4 text-right">Reset</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label><b>Unit</b></label>
                                                        <select class="selectpicker col-md-12" data-style="btn form-control" name="unit_id" id="unit" title="Unit" data-show-tick="true">
                                                            <option disabled>Select a department</option>
                                                        </select>
                                                        <p class="refresh-picker pr-4 text-right">Reset</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mb-2">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label><b>Ends on</b></label>
                                                        <input class="form-control datepicker" name="endDueDate" value="{{$pmSchedule->endDueDate != null ? date('m/d/Y H:i:s', strtotime($pmSchedule->endDueDate)) : null}}"/>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label><b>&nbsp;</b></label>
                                                        <div class="form-check mt-2">
                                                            <label class="form-check-label">
                                                                <input class="form-check-input"  <?php if($pmSchedule->rescheduledBasedOnCompletion == 1){echo 'checked';}?>  name="rescheduledBasedOnCompletion" type="checkbox">
                                                                <span class="form-check-sign"></span>
                                                                Reschedule PM based on completion &nbsp;<i style="cursor:pointer" class="far fa-question-circle" title="Turning this setting on sets the next scheduled PM of this type with respect to the last completion date of this PM type" data-toggle="tooltip"></i>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <div id="category_div">
                                                            <label><b>Associated Equipment Category</b></label>
                                                            <select class="selectpicker col-md-12" data-style="form-control col-md-12" title="Category"
                                                            data-show-tick="true" name="asset_category_id" id="category">
                                                            @if($hospital->asset_categories->count() > 0)
                                                                @foreach($hospital->asset_categories as $category)
                                                                <option  <?php if($category->id == $pmSchedule->asset_category_id){echo 'selected';}?>  value="{{$category->id}}">{{$category->name}}</option>
                                                                @endforeach
                                                            @else 
                                                                <option disabled>No equipment categories recorded</option>
                                                            @endif
                                                            </select>
                                                            <p class="refresh-picker text-right absolute pr-4">Reset</p>
                                                        </div>
                                                        <div id="asset_div" style="display:none;">
                                                            <label><b>Associated Equipment</b></label>
                                                            <select class="selectpicker col-md-12" data-style="form-control col-md-12" title="Equipment"
                                                            data-show-tick="true" name="assets[]" data-live-search="true" id="assets" multiple>
                                                            @if($hospital->assets->count() > 0)
                                                                @foreach($hospital->assets as $asset)
                                                                <option value="{{$asset->id}}">{{$asset->name}}</option>
                                                                @endforeach
                                                            @else 
                                                                <option disabled>No assets recorded</option>
                                                            @endif
                                                            </select>
                                                            <p class="refresh-picker text-right absolute pr-4">Reset</p>
                                                        </div>
                                                        <div class="form-check mt-2">
                                                            <label class="form-check-label">
                                                                <input class="form-check-input" id="toggle_category" type="checkbox">
                                                                <span class="form-check-sign"></span>
                                                                Associate to specific equipment
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label><b>Description</b></label>
                                                        <textarea class="form-control" name="description">{{$pmSchedule->description}}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-5 pull-right pr-4">
                                        <button type="submit" class="btn btn-purple" id="btn_submit">Save</button> 
                                    </div>
                                    </form>
                                </div>
                                <div class="tab-pane" id="tasks">
                                    <h5 class="title">Tasks</h5>
                                    <div class="row">
                                        <div class="col-md-8 col-md-sm-12">
                                            <table class="table table-bordered" id="tasks_table">
                                                <thead>
                                                    <tr>
                                                        <th>Task</th>
                                                        <th class="text-right disabled-sorting">Action</th>
                                                    </tr>
                                                </thead>
                                                <tfoot>
                                                    <tr>
                                                        <th>Task</th>
                                                        <th class="text-right disabled-sorting">Action</th>
                                                    </tr>
                                                </tfoot>
                                                <tbody>
                                                    @foreach($pmSchedule->actions as $action)
                                                    <tr>
                                                       <td>{{$action->name}}</td>
                                                       <td class="text-right">
                                                            <a href="javascript:void(0)" data-toggle="tooltip" title = "Remove" onclick="deleteTask('{{$action->id}}')">
                                                                <i class="fas fa-trash text-danger"></i>
                                                            </a>
                                                            &nbsp;
                                                        </td>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="col-md-4 col-sm-12">
                                            <h6 class="title">Add task</h6>
                                            <form id="add_task">
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <div class="form-group">
                                                            <label>
                                                                Task
                                                            </label>
                                                            <textarea name="name" class="form-control" required></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <button type="submit" class="btn btn-purple">Add Task</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="approve" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method = "post" id="approve_pm">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;<span class="sr-only">Close</span></button>
                    <h3>Approve PM</h3>
                </div>
                <div class="modal-body">
                    <h6>Confirm Approval</h6>
                    <p>Be sure you want to approve this maintenance before you proceed to perform this action</p>
                    <input type="hidden" id="pm_id" name="id"/>
                </div>
                <div class="modal-footer mt-4">
                    <div class="pull-right">
                        <button type="submit" class="btn btn-purple text-right pull-right">Approve</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<div id="decline" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method = "post" id="decline_pm">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;<span class="sr-only">Close</span></button>
                    <h3>Decline PM</h3>
                </div>
                <div class="modal-body">
                    <h6>Confirm Decline</h6>
                    <p>Be sure you want to decline this maintenance before you proceed to perform this action</p>
                    <input type="hidden" id="decline_pm_id" name="id"/>
                </div>
                <div class="modal-footer mt-4">
                    <div class="pull-right">
                        <button type="submit" class="btn btn-danger text-right pull-right">Decline</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<div id="delete-task" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method = "post" id="delete_task_form">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;<span class="sr-only">Close</span></button>
                    <h3>Delete Task</h3>
                </div>
                <div class="modal-body">
                    <h6>Confirm Delete</h6>
                    <p>Are you sure you want to delete this task from the list of tasks to be performed for this preventive maintenance?</p>
                    <input type="hidden" id="delete_task_id" name="id"/>
                </div>
                <div class="modal-footer mt-4">
                    <div class="pull-right">
                        <button type="submit" class="btn btn-danger text-right pull-right">Delete</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('scripts')
    <script src="{{asset('js/moment.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/bootstrap-datetimepicker.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatables.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/bootstrap-selectpicker.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/bootstrap-notify.js')}}" type="text/javascript"></script>
    <script>
    $("[data-toggle='tooltip']").tooltip();
    demo.initDateTimePicker();
    let table = generateDtbl("#history_table", "No preventive maintenances of this type carried out", "Search");
    let task_table = generateDtbl("#tasks_table", "No tasks available for this PM schedule", "Search");
    const approve = (id) => {
        $('#pm_id').val(id);
        $('#approve').modal("show");
    }

    const decline = (id) => {
        $('#decline_pm_id').val(id);
        $('#decline').modal("show");
    }

    $("#edit-pm").on("submit", function(e) {
        e.preventDefault();

        let data = new FormData(this);
        data.append("_method", "put");

        let btn = $(this).find('[type="submit"]');
        submit_file_form("/api/pm-schedule/update/{{$pmSchedule->id}}", "post", data, undefined, btn, true);
    });

    $("#approve_pm").on("submit", function(e){
        e.preventDefault();
        let id = $("#pm_id").val();
        //let data = `hospital_id='{{$user->hospital_id}}'&marked_by='{{$user->id}}'`;
        let data = new FormData(this);
        data.append("hospital_id", "{{$user->hospital_id}}");
        data.append("marked_by", "{{$user->id}}");
        let btn = $(this).find('[type="submit"]');

        //console.log(data);
        submit_file_form(`/api/pm/${id}/approve`, "post", data, undefined, btn, true);
    });

    $('#toggle_category').on("change", function(){
        if($(this).prop("checked") == false){
            $("#asset_div").css("display", "none");
            $("#category_div").css("display", "block");
            $('[name="assets[]"]').val(null);
            $('[name="assets[]"]').selectpicker("refresh");
        }else{
            $("#asset_div").css("display", "block");
            $("#category_div").css("display", "none");
            $('[name="asset_category_id"]').val(null);
            $('[name="asset_category_id"]').selectpicker("refresh");
        }
    });

    $("#department").on("change", function(){
            $("#unit").val(null);
            $("#unit").html(null);

            let units = $(this).find(":selected").data("units");

            if(units.length > 0){
                $("#unit").selectpicker("refresh");

                $.each(units, function(index, unit){
                    $("#unit").append(`<option value="${unit.id}">${unit.name}</option>`);
                });

            }else{
                $("#unit").append(`<option disabled>No units for this department</option>`);
            }

            $("#unit").selectpicker("refresh");
        });

    $("#add_task").on("submit", function(e){
        e.preventDefault();
        console.log("bomboyaya");
        let data = $(this).serialize();
        let btn = $(this).find("[type=submit]");

        submit_form("/api/pm-schedule/{{$pmSchedule->id}}/task/add", "post", data, undefined, btn, true);
    });

    const deleteTask = (id) => {
        $("#delete_task_id").val(id);
        $("#delete-task").modal("show")
    }

    $("#delete_task_form").on("submit", function(e){
        e.preventDefault();
        let btn = $(this).find("[type=submit]");
        const pmAction = $(this).find("[name=id]").val();
        submit_form(`/api/pm-schedule/${pmAction}/task/delete`, "delete", null, undefined, btn, true);
    })
    </script>
@endsection