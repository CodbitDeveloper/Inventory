
@extends('layouts.user-dashboard', ['page_title' => 'Add Preventive Maintenance'])
@section('styles')
<style>
#card-tasks{
    min-height: 250px;
    max-height: 250px;
    overflow-y: auto;
}

.absolute{
    position: absolute;
}

.refresh-picker{
    right: 0;
}

.text-small{
    font-size: 12px;
    font-weight: bold;
    cursor: pointer;
}
</style>
@endsection
@section('content')
    <div class="content">
        <div class="row">
            <div class="col-md-9 col-sm-12">
                <div id="details">
                    <div class="card" data-color="primary">
                        <form method="post" action="#" id="add_pm_form" class="p=4">
                            <div class="card-header">
                                <h4 class="inline-block">
                                    Add Preventive Maintenance (PM)
                                </h4>
                            </div>

                            
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label><b>Title</b> <span class="text-danger">*</span></label>
                                            <input class="form-control" name="title" required/>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label><b>Recurrence frequency</b>  <span class="text-danger">*</span></label>
                                            <select class="selectpicker col-sm-12" data-style="form-control" title="Recurring frequency"
                                             data-show-tick="true" name="recurringSchedule" required>
                                                <option value="daily">Daily</option>
                                                <option value="weekly">Weekly</option>
                                                <option value="bi-weekly">Bi-weekly</option>
                                                <option value="monthly">Monthly</option>
                                                <option value="bi-monthly">Bi-monthly</option>
                                                <option value="quarterly">Quarterly</option>
                                                <option value="triannually">Triannually</option>
                                                <option value="biannually">Biannually</option>
                                                <option value="yearly">Yearly</option>
                                                <option value="biennially">Biennially</option>
                                            </select>
                                            <p class="refresh-picker text-right pr-4">Reset</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label><b>Priority</b></label>
                                            <select class="selectpicker col-sm-12" data-style="form-control" title="Priority"
                                             data-show-tick="true" name="priority_id">
                                                @foreach($hospital->priorities as $priority)
                                                <option value="{{$priority->id}}">{{$priority->name}}</option>
                                                @endforeach
                                            </select>
                                            <p class="refresh-picker text-right pr-4">Reset</p>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <div id="category_div">
                                                <label><b>Associated Equipment Category</b></label>
                                                <select class="selectpicker col-md-12" data-style="form-control col-md-12" title="Category"
                                                data-show-tick="true" name="asset_category_id" id="category">
                                                @if($hospital->asset_categories->count() > 0)
                                                    @foreach($hospital->asset_categories as $category)
                                                    <option value="{{$category->id}}">{{$category->name}}</option>
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
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label><b>Ends on</b>  &nbsp;<i style="cursor:pointer" class="far fa-question-circle" title="Select a date if you want to end this preventive maintenance cycle on a particular date" data-toggle="tooltip"></i></label>
                                            <input class="form-control datepicker" name="endDueDate"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 pr-1">
                                        <div class="form-group">
                                            <label><b>Department</b></label>
                                            <select class="selectpicker col-md-12" data-style="btn form-control" name="department_id" title="Department" id="department" data-show-tick="true">
                                            @if($hospital->departments->count() > 0)
                                                @foreach($hospital->departments as $department)
                                                <option value="{{$department->id}}" data-units = "{{$department->units}}">{{$department->name}}</option>
                                                @endforeach
                                            @else
                                                <option disabled>No known departments</option>
                                            @endif
                                            </select>
                                            <p class="refresh-picker pr-4 text-right">Reset</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4 pr-1">
                                        <div class="form-group">
                                            <label><b>Unit</b></label>
                                            <select class="selectpicker col-md-12" data-style="btn form-control" name="unit_id" id="unit" title="Unit" data-show-tick="true">
                                                <option disabled>Select a department</option>
                                            </select>
                                            <p class="refresh-picker pr-4 text-right">Reset</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4 pr-1">
                                        <div class="form-group">
                                            <label><b>Next Due Date</b> <span class="text-danger">*</span>  &nbsp;<i style="cursor:pointer" class="far fa-question-circle" title="Select the date and time for the next PM of this type" data-toggle="tooltip"></i></label>
                                            <input name="due_date" class="form-control datetimepicker" />
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label><b>Description</b></label>
                                            <textarea class="form-control" name="description"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label><b>&nbsp;</b></label>
                                            <div class="form-check mt-2">
                                                <label class="form-check-label">
                                                    <input class="form-check-input" name="rescheduledBasedOnCompletion" type="checkbox">
                                                    <span class="form-check-sign"></span>
                                                    Reschedule PM based on completion &nbsp;<i style="cursor:pointer" class="far fa-question-circle" title="Turning this setting on sets the next scheduled PM of this type with respect to the last completion date of this PM type" data-toggle="tooltip"></i>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer">
                                <div class="d-block">
                                    <p class="text-muted text-small">All fields marked (<span class="text-danger">*</span>) are mandatory</p>
                                </div>
                                <div class="pull-right">
                                    <input type='reset' class='btn btn-wd' value='Reset' id="btn_reset"/>
                                    <button type='submit' class='btn btn-purple btn-wd' id="btn_save">Save</button>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-12">
                <div id="tasks">
                    <div class="card" data-color="primary">
                        <div class="card-header">
                            <h6 class="inline-block">
                                PM Tasks
                            </h6>
                        </div>

                        
                        <div class="card-body" id="card-tasks">
                            
                        </div>

                        <div class="card-footer">
                            <form id="add_task" action="javascript:void(0)">
                                <textarea class="form-control" placeholder="Add Task" id="task"></textarea>
                                <button type="submit" class="btn btn-round btn-purple mt-2 pull-right mb-3">Add Task</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>  
@endsection
@section('scripts')
    <script src="{{asset('js/moment.min.js')}}"></script>
    <script src="{{asset('js/bootstrap-datetimepicker.js')}}"></script>
    <script src="{{asset('js/bootstrap-selectpicker.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/bootstrap-notify.js')}}" type="text/javascript"></script>
    <script>
        $("[data-toggle='tooltip']").tooltip();
        demo.initDateTimePicker();
        let tasks = [];

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
            const task = $("#task").val(); 
            tasks.push(task);

            const index = tasks.length - 1;

            $('#card-tasks').append(`<div class="col-md-12">
                <span class="text-small">${task}</span>
                <p class="text-right text-danger text-small" onclick="removeFromTasks(${index}, this)">Remove</p>
                <hr/>
            </div>`);

            $(this).trigger("reset");
        });
        
        const removeFromTasks = (index, element) => {
            tasks = tasks.slice(0, index).concat(tasks.slice(index + 1, tasks.length));
            $(element).closest("div").remove();
        }

        $("#add_pm_form").on("submit", function(e){
            e.preventDefault();

            let data = new FormData(this);
            let btn = $(this).find("[type='submit']");
            
            if(tasks.length == 0){
                presentNotification("Add at least one task to this pm schedule", "danger", "top", "right");
            }else{
                data.append("hospital_id", "{{$user->hospital_id}}");
                data.append("reported_by", "{{Auth::user()->id}}");
                data.append("actions", tasks);
                
                if(data.get("assets[]") == null && data.get("asset_category_id") == ""){
                    presentNotification("Select at least one equipment or category associated with this preventive maintenance", "danger", "top", "right");
                }else{
                    const success = (data) => {
                        window.location.replace("/pm-schedules")
                    }
                    submit_file_form("/api/pm-schedule/add", "post", data, success, btn, false);
                }
            }
        })
    </script>
@endsection