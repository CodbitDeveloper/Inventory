@extends('layouts.user-dashboard', ['page_title' => $work_order->title])
@section('styles')
<style>
.card-comments .card-body{
    min-height: 350px;
    max-height: 400px;
    overflow-x: auto;
}

.add-asset{
    font-weight: bold;
    padding-left: 4px;
    font-size: 12px;
    cursor: pointer;
    text-decoration: underline;
}

.text-small{
    font-size:12px;
}

.image-preview{
    cursor:pointer;
}

.badge-dark{
    background-color: #777777;
    color: #ffffff;
}
</style>
@endsection
@section('content')
<div class="content">
    <div class="row">
        <div class="col-md-9 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="title">{{$work_order->title}}</h5>
                    <div class="dropdown" style="position:absolute; top: 0; right: 0;">
                        <button type="button"
                            class="btn btn-round btn-default dropdown-toggle btn-simple"
                            data-toggle="dropdown" style="border:none;">
                            Actions
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">
                        @if($work_order->is_complete == 0 || $work_order->is_complete == null)
                            @if($user->id == $work_order->assigned_to)
                            @if($work_order->status !== 2)
                            <button class="dropdown-item" onclick="change_status(2, this)">Mark as <b>in progress</b></button>
                            @endif
                            @if($work_order->status !== 3)
                            <button class="dropdown-item" onclick="change_status(3, this)">Mark as <b>on hold</b></button>
                            @endif
                            @if($work_order->status == 2 || $work_order->status == 3)
                            <button class="dropdown-item"  onclick="change_status(1, this)">Mark as <b>closed</b></button>
                            @endif
                            @endif
                            @if($user->id == $work_order->user_admin && $work_order->status == 1)
                            <button class="dropdown-item" data-toggle="modal" data-target="#complete_modal">Mark as <b>completed</b></button>
                            @endif
                            @if($user->role == 'Admin' && ($work_order->status == 4 || $work_order->status == 3 || $work_order->status == 2))
                            <button class="dropdown-item" data-toggle="modal" data-target="#request_engineer">Request engineer</button>
                            @endif
                        @else
                            <button class="dropdown-item" disabled>No actions available</button>
                        @endif
                        </div>
                    </div>
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
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" role="tablist" href="#pos">Purchase Orders</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" role="tablist" href="#attachments">Attachments</a>
                        </li>
                        @if($work_order->is_complete == 1 && $work_order->assigned_to == $user->id)
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" role="tablist" href="#report">Report</a>
                        </li>
                        @endif
                        @if($work_order->is_complete != 1 && $user->id == $work_order->user_admin)
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" role="tablist" href="#edit">Edit</a>
                        </li>
                        @endif
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
                            @if($user->role == 'Admin')
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
                                        <button class="btn btn-round btn-light btn-icon" data-toggle="modal" data-target="#assign_team"><i class="fas fa-plus"></i></button>
                                    </div>
                                </div>
                            </div>
                            @endif
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <h6><b>Equipment</b></h6>
                                        <span>
                                        @if($work_order->asset != null)
                                            @if($user->role == 'Admin' || $user->role == 'Regular Technician')
                                                <a href="/inventory/{{$work_order->asset->id}}">{{$work_order->asset->name}}</a>
                                            @elseif($user->role == 'Limited Technician')
                                                {{$work_order->asset->name}}
                                            @endif
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
                            <a href="javascript:void(0)" class="btn btn-round pull-right" 
                            data-toggle="modal" data-target="#add_part" style="margin-top: -50px"  @if($work_order->is_complete == 1) disabled @endif>Add Spare Part</a>
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
                        <div class="tab-pane" id="pos">
                            <div class="row">
                                <div class="col-sm-12">    
                                <a href="/purchase-orders/add?work_order={{$work_order->id}}" class="btn btn-round pull-right" 
                                 style="margin-top: -50px"  @if($work_order->is_complete == 1) disabled @endif>Create Purchase Order</a>
                                    @if($work_order->purchase_orders->count() > 0)
                                    <table id="purchase_orders" class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>Title</th>
                                                <th>Order #</th>
                                                <th>Item Count</th>
                                                <th>Total Cost</th>
                                                <th>Requester</th>
                                                <th>Vendor</th>
                                                <th>Created At</th>
                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
                                                <th>Title</th>
                                                <th>Order #</th>
                                                <th>Item Count</th>
                                                <th>Total Cost</th>
                                                <th>Requester</th>
                                                <th>Vendor</th>
                                                <th>Created At</th>
                                            </tr>
                                        </tfoot>
                                        <tbody>
                                            @foreach($work_order->purchase_orders as $order)
                                            <tr>
                                                <td><b><a href="/purchase-order/{{$order->id}}">{{$order->title}}</a></b></td>
                                                <td>{{$order->po_number}}</td>
                                                <td>{{$order->order_items->count()}}</td>
                                                <td>$ {{$order->item_cost + $order->sales_tax + $order->shipping_cost + $order->other_cost}}</td>
                                                <td>{{$order->user->firstname.' '.$order->user->lastname}}</td>
                                                <td>{{$order->service_vendor->name}}</td>
                                                <td>{{date('jS F Y', strtotime($order->created_at))}}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    @else
                                    <p class="text-muted text-center">No purchase orders associated with this work order</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="attachments">
                            <div class="row">
                                <div class="fileinput fileinput-new col-md-6 col-sm-12" data-provides="fileinput">
                                    <div class="col-md-12 form-group">
                                        <label style="display:block;"><b>Attached Image</b></label>
                                        <div class="fileinput fileinput-new text-center" data-provides="fileinput">
                                            <div class="fileinput-new thumbnail">
                                            <div style="display: none; position: absolute; z-index: 110; left: 400; top: 100; width: 15; height: 15" id="preview_div"></div>
                                                <img src="{{asset('img/assets/work_orders/'.$work_order->image)}}" class="image-preview" onerror = "this.src = '{{asset('img/image_placeholder.jpg')}}'"/>
                                            </div>
                                            <div class="fileinput-preview fileinput-exists thumbnail"></div>
                                            <div>
                                                <span class="btn btn-rose btn-round btn-file">
                                                    <span class="fileinput-new">Select image</span>
                                                    <span class="fileinput-exists">Change</span>
                                                    <input type="file" name="image" />
                                                </span>
                                                <a href="#pablo" class="btn btn-danger btn-round fileinput-exists"
                                                    data-dismiss="fileinput"><i class="fa fa-times"></i> Remove</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><b>Attached File</b></label>
                                        @if($work_order->fileName == null)
                                        <p>N/A</p>
                                        <div class="form-group form-file-upload form-file-simple mb-2">
                                            <label><b>Attach New File</b></label>
                                            <input class="form-control inputFileVisible" placeholder="Select file...">
                                            <input type="file" class="inputFileHidden" name="fileName">
                                        </div>
                                        @else
                                        <a href="javascript:void(0)">{{$work_order->fileName}}</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if($work_order->is_complete == 1)
                        <div class="tab-pane" id="report">
                            <form id="report_form">
                                <div class="row mb-4">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label><b>Total Cost of Maintenance</b></label>
                                            <input type="number" step="0.01" name="cost" class="form-control"/>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label><b>Work Hours</b></label>
                                            <input type="number" step="0.01" name="actual_duration" class="form-control"/>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label><b>Date completed</b></label>
                                            <input type="text" name="date_completed" class="form-control datepicker"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label><b>Extra Notes</b></label>
                                            <textarea class="form-control" name="extra_note"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label><b>Equipment status</b></label>
                                            <select class="selectpicker" data-style="form-control" title="Status" name="status">
                                                <option>Good</option>
                                                <option>Bad</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label><b>Availability</b></label>
                                            <select class="selectpicker" data-style="form-control" title="Availability" name="availability">
                                                <option>Operational</option>
                                                <option>Non operational</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row pull-right text-right pr-3">
                                    <button class="btn btn-purple" type="submit">Save</button>
                                </div>
                            </form>
                        </div>
                        @endif
                        @if($work_order->is_complete != 1 && $user->id == $work_order->user_admin)
                        <div class="tab-pane" id="edit">
                            <form id="edit_form">
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label><b>Title</b></label>
                                            <input type="text"  name="title" class="form-control" value="{{$work_order->title}}"/>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label><b>Work order priority</b></label>
                                            <select class="selectpicker" data-style="form-control" title="Priority" name="priority_id" data-show-tick="true">
                                                @foreach($hospital->priorities as $priority)
                                                <option value="{{$priority->id}}" 
                                                    <?php if($work_order->priority_id == $priority->id){echo 'selected';} ?>
                                                    >{{$priority->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label><b>Change Technician</b></label>
                                            <select class="selectpicker" data-style="form-control" title="Lead Technician" 
                                            name="assigned_to" id="lead_tech" data-show-tick="true" data-live-search="true">
                                                <option disabled><i>Fetching technicians...</i></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label><b>Details</b></label>
                                            <textarea class="form-control" name="description">{{$work_order->description}}</textarea> 
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label><b>Fault Type</b></label>
                                            <select class="selectpicker" data-style="form-control" title="Fault Type" 
                                            name="fault_category_id" data-show-tick="true">
                                                @foreach($hospital->fault_categories as $category)
                                                <option value="{{$category->id}}"
                                                <?php if($work_order->fault_category_id == $category->id){echo 'selected';} ?>
                                                >{{$category->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label><b>Due Date</b></label>
                                            <input type="text" class="form-control datepicker" name="due_date" value="{{$work_order->due_date != null ? date('m/d/Y', strtotime($work_order->due_date)) : null}}"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="row pull-right text-right pr-3">
                                    <button class="btn btn-purple" type="submit">Update</button>
                                </div>
                            </form>
                        </div>
                        @endif
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
<div class="modal fade" id="assign_team">
    <div class="modal-dialog">
        <form method = "post" id="add_engineer">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;<span class="sr-only">Close</span></button>
                    <h6 class="header">Add Technicians To Work Order</h6>
                </div>
                <div class="modal-body">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label><b>Select technicians to add</b></label>
                            <select class="selectpicker col-md-12" data-style="form-control" title="Technicians" name="user_ids[]"
                            data-live-search="true" data-show-tick="true" id="technicians" multiple required>
                            <option disabled><i>Fetching Technicians...</i></option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer mt-4 right">
                    <button type="submit" class="btn btn-purple">Add To Work Order</button>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="modal fade" id="assign_asset">
    <div class="modal-dialog">
        <form method = "post" id="add_asset">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;<span class="sr-only">Close</span></button>
                    <h6 class="header">Assign Equip. To Work Order</h6>
                </div>
                <div class="modal-body">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label><b>Select asset</b></label>
                            <select class="selectpicker col-md-12" data-style="form-control" title="Equipment" name="asset_id"
                            data-live-search="true" data-show-tick="true" id="assets" required>
                            <option disabled><i>Fetching equipment...</i></option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer mt-4 right">
                    <button type="submit" class="btn btn-purple">Assign To Work Order</button>
                </div>
            </div>
        </form>
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
<div class="modal fade" id="complete_modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;<span class="sr-only">Close</span></button>
                <h6 class="header">Complete Work Order</h6>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to mark this work order as completed? This process cannot be undone.</p>
            </div>
            <div class="modal-footer mt-4 right">
                <button type="button" data-dismiss="modal" class="btn btn-light">Cancel</button>
                <button type="button" class="btn btn-purple" onclick="complete(this)">Mark as complete</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="request_engineer">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;<span class="sr-only">Close</span></button>
                <h6 class="header">Request Biomedical Engineer</h6>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to request for biomedical engineer?</p>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-row">
                            <div style="width:100%;">
                                <label><b>Description</b></label>
                                <textarea class="form-control resetable" id="request_description"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer mt-4 right">
                <button type="button" data-dismiss="modal" class="btn btn-light">Cancel</button>
                <button type="button" class="btn btn-purple" onclick="request(this)">Request biomedical engineer</button>
            </div>
        </div>
    </div>
</div>
<div id="add_part" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method = "post" id="add_spare_part">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;<span class="sr-only">Close</span></button>
                        <h6 class="header">Add Spare Part</h6>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                <label><b>Select Part</b> <span class="text-danger">*</span></label>
                                <select class="selectpicker col-md-12" data-style="form-control" 
                                name="part_id" id="part_id" title="Select Part" data-live-search="true" required>
                                    @foreach($hospital->parts as $part)
                                    <option value="{{$part->id}}">{{$part->name}}</option>
                                    @endforeach
                                </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-2">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label><b>Quantity</b> <span class="text-danger">*</span></label>
                                    <input type="number" step="1" class="form-control" id="quantity" name="quantity" required/>
                                </div>
                            </div>
                        </div> 
                    </div>
                    <div class="modal-footer mt-4">
                        <div class="pull-right">
                            <button type="submit" class="btn btn-purple text-right pull-right">Add</button>
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

    let parts_table = generateDtbl("#spare-parts", "No parts associated with this work order", "Search parts");
    let orders_table = generateDtbl("#purchase_orders", "No purchase orders created for this work order", "Search");
    $(document).ready(function(){
        fetchTechnicians();
        fetchAssets();
        fetchActivities();
        fetchComments();
        fetchParts();
        demo.initDateTimePicker();
    });

    const fetchTechnicians = () => {
        $.ajax({
            url : '/api/work-order/available-technicians/{{$work_order->id}}',
            data : 'GET',
            success : (data) => {
                if(data.engineers.length == 0){
                    $('#technicians, #lead_tech').html(`<option disabled>No technicians available</option>`);
                }else{
                    $('#technicians, #lead_tech').html(null);

                    @if($user->id == $work_order->assigned_to)
                    $('#lead_tech').append(`<option value="{{$user->id}}" selected>{{$user->firstname.' '.$user->lastname}}</option>`)
                    @endif

                    $.each(data.engineers, function(index, engineer){
                        $('#technicians, #lead_tech').append(`<option value="${engineer.id}">${engineer.firstname} ${engineer.lastname}</option>`);
                    });
                }

                $('#technicians, #lead_tech').selectpicker("refresh");
            },
            error : (xhr) => {
            }
        });
    }

    const fetchAssets = () => {
        $.ajax({
            url : '/api/assets/{{$user->hospital_id}}',
            data : 'GET',
            success : (data) => {
                if(data.length == 0){
                    $('#assets').html(`<option disabled>No assets available</option>`);
                }else{
                    $('#assets').html(null);

                    $.each(data, function(index, asset){
                        $('#assets').append(`<option value="${asset.id}">${asset.name}</option>`)
                    });
                }

                $('#assets').selectpicker("refresh");
            },
            error : (xhr) => {
            }
        });
    }
    
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
    
    $("#add_engineer").on("submit", function(e){
        e.preventDefault();
        let data = new FormData(this);
        data.append("authenticated_user", "{{$user->id}}");
        let btn = $(this).find('[type="submit"]');

        submit_file_form("/api/work-order/{{$work_order->id}}/assign-team", "post", data, undefined, btn, true);
    });

    $("#add_asset").on("submit", function(e){
        e.preventDefault();
        let data = new FormData(this);
        data.append("_method", "put");
        let btn = $(this).find('[type="submit"]');

        submit_file_form("/api/work-order/{{$work_order->id}}/assign-asset", "post", data, undefined, btn, true);
    });

    $("#add_activity_form").on("submit", function(e){
        e.preventDefault();
        let data = new FormData(this);
        data.append("user_id", "{{$user->id}}")
        let btn = $(this).find('[type="submit"]');

        submit_file_form("/api/work-order/{{$work_order->id}}/record-activity", "post", data, undefined, btn, true);
    });

    $("#edit_form").on("submit", function(e){
        e.preventDefault();
        let data = new FormData(this);
        data.append("_method", "put");
        data.append("authenticated_user", "{{$user->id}}");
        let btn = $(this).find('[type="submit"]');

        submit_file_form("/api/work-order/{{$work_order->id}}/update", "post", data, undefined, btn, true);
    });

    $("#add_comment").on("submit", function(e){
        e.preventDefault();
        let data = new FormData(this);
        data.append("user_id", "{{$user->id}}")
        console.log(data.get('user_id'));
        let btn = $(this).find('[type="submit"]');
        
        const success = (data) => {
            if($('#comments').html() == `<p><i><b>No comments made</b></i></p>`){
                $('#comments').html(null);
            }

            $("#comments").append(`<div class="col-md-12">
                <p>${data.comment.comment}<br/>
                <span class="text-small">
                <a href="javascript:void(0)">{{$user->firstname.' '.$user->lastname}}</a> <i>${data.comment.created_at}</i></span>
                </p>
            </div>`);
            $("#comment").html(null);
        }
        submit_file_form("/api/work-order/{{$work_order->id}}/comment", "post", data, success, btn, false);
    });

    $("#add_spare_part").on("submit", function(e){
        e.preventDefault();

        let data = new FormData(this);
        let btn = $(this).find('[type="submit"]');

        submit_file_form("/api/work-order/{{$work_order->id}}/add-part", "post", data, undefined, btn, true);
    });

    const change_status = (status, element) => {
        let data = `status=${status}`;
        $(element).prop("disabled", true);

        success = (data) => {
            $(element).prop("disabled", false);
        }

        submit_form("/api/work-order/{{$work_order->id}}/update-status", "post", data, undefined, null, true);
    }

    const complete = (element) => {
        let btn = $(element);
    
        let data = {
            "approved_by" : "{{$user->id}}"
        }

        submit_form("/api/work-order/{{$work_order->id}}/complete", "post", data, undefined, btn, true);
    }

    const request = (element) => {
        let btn = $(element);

        let data = `work_order_id={{$work_order->id}}&description=${$("#request_description").val()}`;

        submit_form("/api/request-engineer/add", "post", data, undefined, btn, true);
    }

    $("#report_form").on("submit", function(e){
        e.preventDefault();

        let data = new FormData(this);
        let btn = $(this).find('[type="submit"]');

        submit_file_form("/api/work-order/{{$work_order->id}}/save-report", "post", data, undefined, btn, true);
    })
    
    </script>
@endsection