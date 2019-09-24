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
                <div class="row">
                    <div class="col-md-12 center">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="inline-block">Users</h4>
                            </div>
                            <div class="card-body">
                                <table id="datatable" class="table table-striped table-bordered col-md-11"
                                                    cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Region</th>
                                            <th>Role</th>
                                            <th>Email</th>
                                            <th>Profile Status</th>
                                            <th class="disabled-sorting text-right">Action</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>Name</th>
                                            <th>Region</th>
                                            <th>Role</th>
                                            <th>Email</th>
                                            <th>Profile Status</th>
                                            <th class="disabled-sorting text-right">Action</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        @foreach($admins as $admin)
                                        <tr>
                                            <td>{{$admin->firstname.' '.$admin->lastname}}</td>
                                            <td>{{$admin->region->name}}</td>
                                            <td>{{$admin->role}}</td>
                                            <td>{{$admin->email}}</td>
                                            <td>
                                                @if($admin->active == 1)
                                                <span class="badge badge-info">Active</span>
                                                @elseif($admin->active == 0)
                                                <span class="badge badge-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td class="text-right">
                                                <div class="dropdown">
                                                    <button type="button"
                                                        class="btn btn-round btn-default dropdown-toggle btn-simple btn-icon no-caret"
                                                        data-toggle="dropdown">
                                                        <i class="now-ui-icons loader_gear"></i>
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-right">
                                                        <a class="dropdown-item" href="javascript:void(0)" onclick="resetPassword({{$admin}})">Reset Password</a>
                                                        @if($admin->active == 1 && $admin->role == 'Engineer')
                                                            <a class="dropdown-item" href="javascript:void(0)" onclick="deactivateUser({{$admin}})">Deactivate User</a>
                                                        @elseif($admin->active == 0 && $admin->role == 'Engineer')
                                                            <a class="dropdown-item" href="javascript:void(0)" onclick="activateUser({{$admin}})">Activate User</a>
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
                <a href="/admin/engineers/add">
                    <div class="fab">
                        <i class="fas fa-plus"></i>
                    </div>
                </a>
            </div>
        </div>
    </div>
    <div class="modal fade" id="deactivate-user-modal">
        <div class="modal-dialog">
            <div class="modal-content modal-lg">
                <form method = "post" id="deactivate_user">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;<span class="sr-only">Close</span></button>
                        <h6 class="heading">Deactivate user</h6>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to deactivate <span id="user_fullname"></span>'s account?</p>
                        <input type="hidden" id="deactivate_id" name="id"/>
                    </div>
                    <div class="modal-footer mt-4">
                        <div class="pull-right">
                            <button type="submit" class="btn btn-danger text-right pull-right">Deactivate</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="activate-user-modal">
        <div class="modal-dialog">
            <div class="modal-content modal-lg">
                <form method = "post" id="activate_user">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;<span class="sr-only">Close</span></button>
                        <h6 class="heading">Activate user</h6>
                    </div>
                    <div class="modal-body">
                        <p>You are about to activate <span id="user_fullname_active"></span>'s account. Are you sure you want to continue?</p>
                        <input type="hidden" id="activate_id" name="id"/>
                    </div>
                    <div class="modal-footer mt-4">
                        <div class="pull-right">
                            <button type="submit" class="btn btn-info text-right pull-right">Activate</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="reset-user-modal">
        <div class="modal-dialog">
            <div class="modal-content modal-lg">
                <form method = "post" id="reset_password">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;<span class="sr-only">Close</span></button>
                        <h6 class="heading">Reset password</h6>
                    </div>
                    <div class="modal-body">
                        <p>You are about to reset <span id="user_password_reset"></span>'s password. Are you sure you want to continue?</p>
                        <input type="hidden" id="reset_id" name="id"/>
                    </div>
                    <div class="modal-footer mt-4">
                        <div class="pull-right">
                            <button type="submit" class="btn btn-danger text-right pull-right">Reset Password</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @include('layouts.admin_core_scripts')
    <script src="{{asset('js/bootstrap-notify.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatables.js')}}"></script>
    <script>
        $(document).ready(function () {
            $('#datatable').DataTable({
                "pagingType": "full_numbers",
                "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                responsive: true,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Find a hospital",
                }

            });

            var table = $('#datatable').DataTable();

        });

        const deactivateUser = (admin) => {
            $("#user_fullname").html(`${admin.firstname} ${admin.lastname}`);
            $("#deactivate_id").val(admin.id);

            $("#deactivate-user-modal").modal("show");
        }

        const activateUser = (admin) => {
            $("#user_fullname_active").html(`${admin.firstname} ${admin.lastname}`);
            $("#activate_id").val(admin.id);

            $("#activate-user-modal").modal("show");
        }

        const resetPassword = (admin) => {
            $('#user_password_reset').html(`${admin.firstname} ${admin.lastname}`);
            $('#reset_id').val(admin.id);

            $('#reset-user-modal').modal('show');
        }

        $("#deactivate_user").on("submit", function(e) {
            e.preventDefault();

            let btn = $(this).find('[type=submit]');
            const id = $(this).find('[name=id]').val();

            submit_form("/api/admins/deactivate/"+id, 'put', null, undefined, btn, true);
        });

        $('#activate_user').on('submit', function(e){
            e.preventDefault();

            let btn = $(this).find('[type=submit]');
            const id = $(this).find('[name=id]').val();

            submit_form('/api/admins/activate/'+id, 'put', null, undefined, btn, true);
        });

        $('#reset_password').on('submit', function(e) {
            e.preventDefault();

            let btn = $(this).find('[type=submit]');
            const id = $(this).find('[name=id]').val();

            submit_form('/api/admins/reset-password/'+id, 'put', null, undefined, btn, true);
        });
    </script>
</body>

</html>