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
        <div class="main-panel">
            <div class="panel-header panel-header-sm">
            </div>
            <div class="content">
                <div class="row">
                    <div class="col-md-8">
                        <div class="card"><div class="card-header">
                                <h5 class="title">Complete Profile</h5>
                            </div>
                            <form method="post" action="#" id="profile_update_form">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 pr-1">
                                            <div class="form-group">
                                                <label><b>First Name</b></label>
                                                <input type="text" class="form-control" placeholder="First Name" value="{{$admin->firstname}}" name="firstname" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6 pl-1">
                                            <div class="form-group">
                                                <label><b>Last Name</b></label>
                                                <input type="text" class="form-control" placeholder="Last Name" value="{{$admin->lastname}}" name="lastname" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 pr-1">
                                            <div class="form-group">
                                                <label><b>Phone Number</b></label>
                                                <input type="text" class="form-control" placeholder="0245668999" value="{{$admin->phone_number}}" name="phone_number" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Email</label>
                                                <input type="text" class="form-control" placeholder="Email" value="{{$admin->email}}" name="email" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-8 pr-1">
                                            <div class="form-group">
                                                <label>Role</label>
                                                <p class="form-control" name="role" disabled>{{$admin->role}}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <h5 class="title">Set Password</h5>
                                    <div class="row">
                                        <div class="col-md-6 px-1">
                                            <label>New Password</label>
                                            <div class="form-group">
                                                <input type="password" class="form-control" name="password" id="new_password">
                                                <p class="text-danger text-center warning" style="font-size:11px; display:none">The passwords you have provided do not match</p>
                                            </div>
                                        </div>
                                        <div class="col-md-6 px-1">
                                            <label>Confirm Password</label>
                                            <div class="form-group">
                                                <input type="password" class="form-control" id="confirm_password"  name="password_confirmation"/>
                                                <p class="text-danger text-center warning" style="font-size:11px; display:none;">The passwords you have provided do not match</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-footer text-center">
                                    <button type="submit" id="btn_submit" class="btn btn-wd btn-purple">Save</button>
                                </div>
                            </form>
                            
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card card-user">
                            <div class="image">
                            </div>
                            <div class="card-body">
                                <div class="author">
                                <img class="round" width="96" height="96" avatar="{{$admin->firstname}} {{$admin->lastname}}" />
                                    <h5 class="title" id="card-fullname">{{ucfirst($admin->firstname)}} {{ucfirst($admin->lastname)}}</h5>
                                    <p class="description" id="card-username">
                                        {{$admin->email}}
                                    </p>
                                </div>
                                <p class="description text-center">
                                    {{ucfirst($admin->role)}}
                                    <br> {{$admin->region->name}}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            @include('layouts.admin_core_scripts')
            <script src="{{asset('js/bootstrap-notify.js')}}" type="text/javascript"></script>
            <script>
                $('#profile_update_form').on('submit', (e) => {
                    e.preventDefault();
                    
                    $(".warning").css("display", "none");

                    const password = $("#new_password").val();
                    const confirm = $("#confirm_password").val();

                    if(password === confirm){
                        var form_data = {
                            'id' : '{{$admin->id}}'
                        };

                        //TODO : do validations;
                        
                        
                        $.each($('.form-control'), function(i, el){
                            form_data[$(el).attr('name')] = $(el).val();
                        });

                        $('#btn_submit').html('<i class="now-ui-icons loader_refresh spin"></i>');

                        $.ajax({
                            url: '/api/admin/complete-profile',
                            method: 'post',
                            data: form_data,
                            success: (data, status) => {
                                $('#btn_submit').html('Save');
                                $('#profile_update_form').find('input, select').prop('disabled', false);
                                $('#profile_update_form').find('.resetable').val('');
                                $('#btn_reset').val('Reset');
                                presentNotification('User profile saved', 'info', 'top', 'right');
                                setTimeout(()=>{
                                    location.replace("/admin")
                                }, 500);
                            },
                            error: function(xhr, desc, err){
                                $('#btn_submit').html('Save');
                                $('#profile_update_form').find('.resetable').prop('disabled', false);
                                presentNotification('Could not save this user profile. Try again.', 'danger', 'top', 'right');
                            }
                        });
                    }else{
                        $(".warning").css("display", "block");
                    }
                });
            </script>
        </body>
</html>