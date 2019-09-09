
@extends('layouts.user-dashboard', ['page_title' => 'New PM Report ('.$pmSchedule->title.')'])
@section('styles')
<style>
#card-tasks{
    min-height: 300px;
    max-height: 300px;
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
                                    New PM Report ({{$pmSchedule->title}})
                                </h4>
                            </div>

                            
                            <div class="card-body">
                                <div class="row mb-2">
                                    <div class="col-md-8"></div>
                                    <div class="col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label><b>Date completed</b> <span class="text-danger">*</span></label>
                                            <input class="form-control datepicker" name="date_completed" required/>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label><b>Observation</b></label>
                                            <textarea class="form-control" name="observation"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label><b>Actions Taken</b></label>
                                            <textarea class="form-control" name="action_taken"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label><b>Recommendations</b></label>
                                            <textarea class="form-control" name="recommendation"></textarea>
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
                                Tasks
                            </h6>
                        </div>

                        
                        <div class="card-body" id="card-tasks">
                            <div class="table-full-width table-responsive">
                                <table class="table">
                                    <tbody>
                                        @foreach($pmSchedule->actions()->get() as $action)
                                        <tr>
                                            <td>
                                                <div class="form-check">
                                                    <label class="form-check-label">
                                                        <input class="form-check-input" type="checkbox">
                                                        <span class="form-check-sign"></span>
                                                    </label>
                                                </div>
                                            </td>
                                            <td class="text-left">{{$action->name}}</td>
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
@endsection
@section('scripts')
    <script src="{{asset('js/moment.min.js')}}"></script>
    <script src="{{asset('js/bootstrap-datetimepicker.js')}}"></script>
    <script src="{{asset('js/bootstrap-selectpicker.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/bootstrap-notify.js')}}" type="text/javascript"></script>
    <script>
        $("[data-toggle='tooltip']").tooltip();
        demo.initDateTimePicker();
        
        $("#add_pm_form").on("submit", function(e){
            e.preventDefault();
            let data = new FormData(this);
            let btn = $(this).find("[type='submit']");
            
            if($('.form-check-input:checked').length != $('.form-check-input').length){
                presentNotification("Make sure all tasks are checked out before you continue", "danger", "top", "right");
            }else{
                data.append("pm_schedule_id", "{{$pmSchedule->id}}");
                data.append("recorded_by", "{{Auth::user()->id}}");

                const success = (data) => {
                    setTimeout(() => {
                        location.replace("/pm-schedule/record");
                    }, 500);
                }
                
                submit_file_form("/api/pm/add", "post", data, success, btn, false);
            }
            
        })
    </script>
@endsection