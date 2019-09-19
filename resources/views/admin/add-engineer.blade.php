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
            @include('layouts.admin_navbar', ['page_title' => 'Users'])
            <div class="panel-header panel-header-sm">
            </div>
            <div class="content">
                <div class="col-md-12 mr-auto ml-auto">
                    <div>
                        <div class="card" data-color="primary">
                            <form method="post" action="#" id="add_user_form" class="p=4">
                                <div class="card-header">
                                    <h4 class="inline-block">
                                        New User
                                    </h4>
                                </div>

                                
                            <div class="card-body">
                                    <div class="row mb-4">
                                        <div class="col-md-6 pr-1">
                                            <div class="form-group">
                                                <label><b>Email address</b> <span class="text-danger">*</span></label>
                                                <input type="email" class="form-control form-line resetable" placeholder="Email Address" name="email" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <p class="text-muted"><b>Personal Information</b></p>
                                    </div>
                                    <div class="row">
                                        
                                        
                                        <div class="col-md-6 pr-1">
                                            <div class="form-group">
                                                <label><b>First Name</b></label>
                                                <input type="text" class="form-control resetable" name="firstname">
                                            </div>
                                        </div>
                                        <div class="col-md-6 pr-1">
                                            <div class="form-group">
                                                <label><b>Last Name</b></label>
                                                <input type="text" class="form-control resetable" name="lastname">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 pr-1">
                                            <div class="form-group">
                                                <label><b>Phone</b></label>
                                                <input type="tel" class="form-control resetable" name="phone_number">
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

            </div>
            
            @include('layouts.admin_core_scripts')
            <script src="{{asset('js/bootstrap-selectpicker.js')}}" type="text/javascript"></script>
            <script src="{{asset('js/bootstrap-notify.js')}}" type="text/javascript"></script>
            <script>
                $('#add_user_form').on('submit', function(e){
                    e.preventDefault();
                    var form_data = $(this).serialize();
                    form_data+='&region_id={{$admin->region_id}}'+'&role=Biomedical Engineer';
                    $(this).find('input, select').prop('disabled',true);

                    $('#btn_save').html('<i class="now-ui-icons loader_refresh spin"></i>');

                    $.ajax({
                        url: '/api/admins/add_admin',
                        method: 'post',
                        data: form_data,
                        success: function(data, status){
                            $('#btn_save').html('Save');
                            $('#add_user_form').find('input, select').prop('disabled', false);
                            $('#add_user_form').find('.resetable').val('');
                            $('#btn_reset').val('Reset');
                            presentNotification('User saved', 'info', 'top', 'right');
                            console.log('done');
                        },
                        error: function(xhr, desc, err){
                            $('#btn_save').html('Save');
                            $('#add_user_form').find('.resetable').prop('disabled', false);
                            presentNotification('Could not save this user. Try again.', 'danger', 'top', 'right');
                        }
                    });
                });
            </script>
            
</body>
</html>