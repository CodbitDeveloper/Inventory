<?php use App\Category; ?>
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
            @include('layouts.admin_navbar', ['page_title' => $hospital->name])
            <div class="panel-header panel-header-sm">
            </div>
            <div class="content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card ">
                            <div class="card-header ">
                                <h4 class="card-title">{{$hospital->name}}</h4>
                            </div>
                            <div class="card-body ">
                                <div class="row">
                                    <div class="col-md-2">
                                        <ul class="nav nav-pills nav-pills-primary nav-pills-icons flex-column" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link active" data-toggle="tab" href="#inventory" role="tablist">
                                                    <i class="now-ui-icons ui-2_settings-90"></i>
                                                    Inventory
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" data-toggle="tab" href="#departments" role="tablist">
                                                    <i class="now-ui-icons ui-2_settings-90"></i>
                                                    Departments
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" data-toggle="tab" href="#details" role="tablist">
                                                    <i class="now-ui-icons travel_info"></i>
                                                    Details
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-md-10">
                                        <div class="tab-content">
                                            <div class="tab-pane active" id="inventory">
                                                <div id="inventory-tab-main" style="display:none;">
                                                    <table id="equipment_table" class="table table-bordered col-md-11"
                                                        cellspacing="0" width="100%">
                                                        <thead>
                                                            <tr>
                                                                <th>Code</th>
                                                                <th>Name</th>
                                                                <th>Serial No.</th>
                                                                <th>Model No.</th>
                                                                <th>Manufacturer</th>
                                                                <th>Status</th>
                                                                <th>Type</th>
                                                            </tr>
                                                        </thead>
                                                        <tfoot>
                                                            <tr>
                                                                <th>Code</th>
                                                                <th>Name</th>
                                                                <th>Serial No.</th>
                                                                <th>Model No.</th>
                                                                <th>Manufacturer</th>
                                                                <th>Status</th>
                                                                <th>Type</th>
                                                            </tr>
                                                        </tfoot>
                                                        <tbody></tbody>
                                                    </table>
                                                </div>
                                                <div id="inventory-tab-spinner">
                                                    <div class="col-md-12">
                                                        <p class="text-center"><i class="now-ui-icons education_atom spin" style="font-size: 24px;"></i></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane" id="departments">
                                                <div id="department-tab-main" style="display:none;">
                                                    <div class="col-md-12">
                                                        <table id="departments_table" class="table table-hover table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th>Department Name</th>
                                                                    <th>Units</th>
                                                                </tr>
                                                            </thead>
                                                            <tfoot>
                                                                <tr>
                                                                    <td>Department Name</td>
                                                                    <td>Units</td>
                                                                </tr>
                                                            </tfoot>
                                                            <tbody></tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div id="department-tab-spinner">
                                                    <div class="col-md-12">
                                                        <p class="text-center"><i class="now-ui-icons education_atom spin" style="font-size: 24px;"></i></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane" id="details">
                                                <div class="card-header text-center">
                                                    <h4 class="title">{{$hospital->name}}</h4>
                                                    <h5 class="subtitle text-lighted">{{$hospital->address}}</h5>
                                                    <h5 class="subtitle text-lighted">{{$hospital->district->name}}</h5>
                                                    <h5 class="subtitle text-lighted">{{$hospital->contact_number}}</h5>
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
    </div>
    @include('layouts.admin_core_scripts')
    <script src="{{asset('js/datatables.js')}}"></script>
    <script src="{{asset('js/bootstrap-selectpicker.js')}}"></script>
    <script src="{{asset('js/bootstrap-notify.js')}}" type="text/javascript"></script>
    <script>
        const equipment_table = generateDtbl("#equipment_table", "No eqiupment for this hospital");
        const depts_table = generateDtbl("#departments_table", "No departments for this hospital");

        $(document).ready(function(){
            $.ajax({
                "method" : "get",
                "url" : "/api/hospitals/{{$hospital->id}}/get-equipment",
                "success" : (data) => {
                    let table_data = [];
                    data.forEach(function(equipment, index){
                        let temp = [equipment.asset_code, equipment.name, equipment.serial_number, equipment.model_number, equipment.manufacturer_name, equipment.status, equipment.availability, equipment.asset_category == null ? 'N/A' : equipment.asset_category.name];
                        table_data.push(temp);
                    });
                    equipment_table.rows.add(table_data).draw();

                    $("#inventory-tab-main").css("display", "block");
                    $("#inventory-tab-spinner").css("display", "none")
                }
            });

            $.ajax({
                "method" : "get",
                "url" : "/api/hospitals/{{$hospital->id}}/get-departments",
                "success" : (data) => {
                    let table_data = [];
                    data.forEach(function(department, index){
                        let ul = "";
                        department.units.forEach(function(unit, index){
                            ul += `<li>${unit.name}</li>`
                        });

                        let temp = [department.name, `<ul>${ul}</ul>`];
                        table_data.push(temp);
                    });
                    depts_table.rows.add(table_data).draw();

                    $("#department-tab-main").css("display", "block");
                    $("#department-tab-spinner").css("display", "none")
                }
            });
        })
    </script>
</body>

</html>