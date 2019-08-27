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
    <div class="wrapper">
        @include('layouts.admin_sidebar')
        <div class="main-panel">
            @include('layouts.admin_navbar', ['page_title' => $equipment->name])
            <div class="panel-header panel-header-sm">
            </div>
            <div class="content">
                <div class="row">
                    <div class="col-md-3">
                        <div class="">
                            <img src="{{asset('img/assets/equipment/'.$equipment->image)}}" onerror = "this.src = '{{asset('img/image_placeholder.jpg')}}'"/>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="title pb-2">{{$equipment->name}} - <b>{{$equipment->equipment_code}}</b></h5>
                                <p style="position:absolute; top: 0; right: 0; margin: 16px;">
                                    @if($equipment->status == "Good")
                                    <span><i class="fas fa-circle text-success pulse"></i> Good</span>
                                    @else
                                    <span><i class="fas fa-circle text-danger pulse"></i> Bad
                                    </span>
                                    @endif
                                </p>
                                <ul class="nav nav-tabs nav-tabs-primary text-center mt-2" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-toggle="tab" href="#details" role="tablist">
                                            Details
                                        </a>
                                    </li>
                                    @if($admin->role == 'Admin')
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#edit" role="tablist">
                                            Edit
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#equipment_status" role="tablist">
                                            Status
                                        </a>
                                    </li>
                                    @endif
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content tab-space">
                                    <div class="tab-pane active" id="details">
                                        <div class="row pl-4">
                                            <div class="col-md-3">
                                                <div id="qrcode"></div>
                                                <button class="btn btn-purple btn-block" onclick="printContent('qrcode')">Print QR Code</button>
                                            </div>
                                            <div class="col-md-9">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="description-label">Warranty Expiration</label>
                                                            <p class="text-highlighted">
                                                                {{$equipment->warranty_expiration != null && !Carbon\Carbon::parse($equipment->warranty_expiration)->isPast() ? Carbon\Carbon::parse($equipment->warranty_expiration)->diffForHumans() : 'No warranty available'}}
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label class="description-label">Equipment Code</label>
                                                            <p class="no-border"><b>{{$equipment->equipment_code}}</b></p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label class="description-label">Availability</label>
                                                            <p class="no-border">{{$equipment->donation == null ? 'Available' : 'Donated on '.date('jS F, Y', strtotime($equipment->donation->date_donated)) }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label class="description-label">Serial Number</label>
                                                            <p class="no-border">{{$equipment->serial_number == null ? "N/A" : $equipment->serial_number}}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label class="description-label">Model Number</label>
                                                            <p class="no-border">{{$equipment->model_number == null ? "N/A" : $equipment->model_number}}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label class="description-label">Category</label>
                                                            <p class="no-border">{{$equipment->admin_category == null ? "N/A" : $equipment->admin_category->name}}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label class="description-label">Manufacturer</label>
                                                            <p class="no-border">{{$equipment->manufacturer_name == null ? "N/A" : $equipment->manufacturer_name}}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label class="description-label">Procurement Type</label>
                                                            <p class="no-border">{{$equipment->procurement_type == null ? "N/A" : $equipment->procurement_type}}</p>
                                                        </div>
                                                    </div>
                                                    @if($equipment->procurement_type == "Donation")
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label class="description-label">Donor</label>
                                                            <p class="no-border">{{$equipment->donor == null ? "N/A" : $equipment->donor}}</p>
                                                        </div>
                                                    </div>
                                                    @else
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label class="description-label">Purchase Price</label>
                                                            <p class="no-border">{{$equipment->purchase_price == null ? "N/A" : $equipment->purchase_price}}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label class="description-label">Purchase Date</label>
                                                            <p class="no-border">{{$equipment->pruchase_date == null ? "N/A" : date('jS F, Y', strtotime($equipment->purchase_date))}}</p>
                                                        </div>
                                                    </div>
                                                    @endif
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="description-label">Description</label>
                                                            <textarea class="form-control" rows="4" readonly>{{$equipment->description != null ? $equipment->description : 'N/A'}}</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="description-label">Location</label>
                                                            <textarea class="form-control" rows="4" readonly>{{$equipment->area != null ? $equipment->area : 'N/A'}}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="equipment_status">
                                        <form id="change_status">
                                            <div class="row pl-4">
                                                <div class="col-md-12">
                                                    <div class="row mb-2">
                                                        <div class="col-md-5">
                                                            <div class="form-group">
                                                                <label class="description-label">Status</label>
                                                                <div class="input-group">
                                                                    <select class="col-md-12 selectpicker" data-style="form-control" data-show-tick="true"
                                                                    name="status" title="Status" required>
                                                                        <option <?php if($equipment->status == "Good"){echo "selected";}?>>Good</option>
                                                                        <option <?php if($equipment->status == "Bad"){echo "selected";}?>>Bad</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12 mt-4">
                                                            <div class="form-group">
                                                                <label class="description-label">Reason</label>
                                                                <textarea class="form-control" name="reason"></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <button class="btn btn-purple" type="submit">Update</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="tab-pane" id="edit">
                                        <form id="edit_equipment">
                                            <div class="form-row">
                                                <div class="form-group col-md-4">
                                                    <label><b>Equipment Name</b> <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control resetable" name="name" value="{{$equipment->name}}" required/>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label><b>Serial Number</b></label>
                                                    <input type="text" class="form-control resetable" name="serial_number" value="{{$equipment->serial_number}}"/>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label><b>Model Number</b></label>
                                                    <input type="text" class="form-control resetable" name="model_number" value="{{$equipment->model_number}}"/>
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="form-group col-md-4">
                                                    <label><b>Equipment Category</b> <span class="text-danger">*</span></label>
                                                    <select class="selectpicker col-sm-12" title="Category" data-style="form-control" name="admin_category_id">
                                                        @foreach($categories as $category)
                                                            <option value="{{$category->id}}" <?php if($equipment->admin_category_id == $category->id){echo 'selected';}?>>{{$category->name}}</option>
                                                        @endforeach
                                                    </select>
                                                    <p class="refresh-picker pr-4 text-right">Reset</p>
                                                </div>
                                                @if($equipment->procurement_type == "Donation")
                                                <div class="form-group col-md-4">
                                                    <label><b>Donor</b></label>
                                                    <input type="text" class="form-control" name="donor" value="{{$equipment->donor}}">
                                                </div>
                                                @else
                                                <div class="form-group col-md-4">
                                                    <label><b>Purchase Price</b></label>
                                                    <input type="number" step="0.01" class="form-control resetable" value="{{$equipment->purchase_price}}" name="purchase_price">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label><b>Purchase Date</b></label>
                                                    <select class="selectpicker col-sm-12" title="Type" data-style="form-control" name="type">
                                                        <option <?php if($equipment->type == "Equipment"){echo 'selected';}?>>Equipment</option>
                                                        <option <?php if($equipment->type == "Spare Part"){echo 'selected';}?>>Spare Part</option>
                                                    </select>
                                                </div>
                                                @endif
                                            </div>
                                            <div class="form-row">
                                                <div class="form-group col-md-4 col-sm-12">
                                                    <label><b>Warranty Expiration</b></label>
                                                    <input type="text" class="form-control datepicker" name="warranty_expiration" value="{{$equipment->warranty_expiration == null ? null : Carbon\Carbon::parse($equipment->warranty_expiration)->format('m/d/Y')}}"/>
                                                </div>
                                                <div class="form-group col-md-4 col-sm-12">
                                                    <label><b>Manufacturer Name</b></label>
                                                    <input type="text" class="form-control" name="manufacturer_name" value="{{$equipment->manufacturer_name}}"/>
                                                </div>
                                                <div class="form-group col-md-4 col-sm-12">
                                                    <label><b>Type</b></label>
                                                    <input type="text" class="form-control" name="manufacturer_name" value="{{$equipment->manufacturer_name}}"/>
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="form-group col-md-4 mt-4 col-sm-12">
                                                    
                                                    <label><b>Description</b></label>
                                                    <textarea class="form-control resetable" name="description" rows="3">{{$equipment->description}}</textarea>
                                                    
                                                    <button type="submit" class="btn btn-purple mt-5" id="btn_submit">Save</button>
                                                </div>
                                                <div class="form-group col-md-4 mt-4 col-sm-12">
                                                    <label><b>Location</b></label>
                                                    <textarea class="form-control resetable" name="area" rows="3">{{$equipment->area}}</textarea>
                                                </div>
                                                <div class="fileinput fileinput-new col-md-4 col-sm-12" data-provides="fileinput">
                                                    <div class="col-md-12 form-group">
                                                            <label style="display:block;"><b>Image</b></label>
                                                            <div class="fileinput fileinput-new text-center" data-provides="fileinput">
                                                                <div class="fileinput-new thumbnail">
                                                                    <img src="{{asset('img/assets/equipment/'.$equipment->image)}}" onerror = "this.src = '{{asset('img/image_placeholder.jpg')}}'"/>
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
    @include('layouts.admin_core_scripts')
    <script src="{{asset('js/moment.min.js')}}"></script>
    <script src="{{asset('js/qrcode.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/bootstrap-datetimepicker.js')}}"></script>
    <script src="{{asset('js/bootstrap-selectpicker.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/bootstrap-notify.js')}}" type="text/javascript"></script>
    <script>
        demo.initDateTimePicker();
        
        if($('#qrcode').html() == ''){
            qr = new QRCode(document.getElementById("qrcode"), "{{$equipment->id}}");
        }

        function printContent(el){
            var restorepage = $('body').html();
            var printcontent = $('#' + el).clone();
            $('body').empty().html(printcontent);
            window.print();
            window.location.reload();
        }

        $("#edit_equipment").on("submit", function(e){
            e.preventDefault();

            let data = new FormData(this);
            data.append("_method", "put");

            let btn = $(this).find('[type="submit"]');
            submit_file_form("/api/equipment/{{$equipment->id}}/update", "post", data, undefined, btn, true);
        });

        $("#change_status").on("submit", function(e){
            e.preventDefault();
            let data = $(this).serialize();
            let btn = $(this).find('[type="submit"]');
            submit_form("/api/equipment/{{$equipment->id}}/update-status", "put", data, undefined, btn, true);
        });

    </script>
</body>

</html>