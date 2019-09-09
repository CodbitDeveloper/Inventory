    @extends('layouts.user-dashboard', ['page_title' => 'Users'])
    @section('styles')
    <link href="https://cdn.datatables.net/buttons/1.5.6/css/buttons.dataTables.min.css" rel="stylesheet" />
    @endsection
    @section('content')
    <div class="content">
        <div class="row">
            <div class="col-md-12 center">
                <div class="card">
                    <div class="card-header">
                        <h4 class="inline-block">System Users</h4>
                    </div>
                    <div class="card-body">
                        <table id="datatable" class="table table-bordered" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email Address</th>
                                    <th>Role</th>
                                    <th>Phone No.</th>
                                    <th>Job Title</th>
                                    <th>Profile Status</th>
                                    <th class="disabled-sorting text-right">Action</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th>Name</th>
                                    <th>Email Address</th>
                                    <th>Role</th>
                                    <th>Phone No.</th>
                                    <th>Job Title</th>
                                    <th>Profile Status</th>
                                    <th class="disabled-sorting text-right">Action</th>
                                </tr>
                            </tfoot>
                            @foreach($users as $user)
                            <tbody>
                                <tr class="uppercase">
                                    <td>
                                        {{$user->firstname.' '. $user->lastname}}
                                    </td>
                                    <td style="text-transform:lowercase;">{{$user->email}}</td>
                                    <td>{{$user->role}}</td>
                                    <td>{{$user->phone_number}}</td>
                                    <td>{{$user->job_title}}</td>
                                    <td>
                                        @if($user->active == 1)
                                        <span class="badge badge-info">Active</span>
                                        @elseif($user->active == 0)
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
                                                <a class="dropdown-item" href="javascript:void(0)" onclick="editUser({{$user}})">Edit User</a>
                                                <a class="dropdown-item" href="javascript:void(0)" onclick="resetPassword({{$user}})">Reset Password</a>
                                                @if($user->active == 1)
                                                <a class="dropdown-item text-danger" href="javascript:void(0)" onclick="deactivateUser({{$user}})">Deactivate User</a>
                                                @elseif($user->active == 0)
                                                <a class="dropdown-item text-info" href="javascript:void(0)" onclick="activateUser({{$user}})">Activate User</a>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <a href="/users/add">
            <div class="fab">
                <i class="fas fa-plus"></i>
            </div>
        </a>
    </div>
    <div class="modal fade" id="edit-user-modal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method = "post" id="edit_user">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;<span class="sr-only">Close</span></button>
                        <h6 class="heading">Edit user</h6>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-2">
                            <div class="col-md-6 pr-1">
                                <div class="form-group">
                                    <label><b>Email address</b> <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control form-line resetable" placeholder="Email Address" name="email" readonly>
                                </div>
                            </div>
                            <div class="col-md-5 pl-1">
                                <div class="form-group">
                                    <label class="pl-3"><b>Role</b>  <span class="text-danger">*</span></label>
                                    <select class="selectpicker col-md-12" data-style="form-control" name="role" title="System Role" required>
                                        <option>Regular Technician</option>
                                        <option>Limited Technician</option>
                                        <option>Hospital Head</option>
                                        <option>View Only</option>
                                    </select>
                                </div>
                            </div>
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
                            <div class="col-md-6 pr-1">
                                <div class="form-group">
                                    <label><b>Job Title</b></label>
                                    <input type="tel" class="form-control resetable" name="job_title">
                                </div>
                            </div>
                            <input type="hidden" name="id"/>
                        </div>
                    </div>
                    <div class="modal-footer mt-4">
                        <div class="pull-right">
                            <button type="submit" class="btn btn-purple text-right pull-right">Edit</button>
                        </div>
                    </div>
                </form>
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
    @endsection
    @section('scripts')
    <script src="{{asset('js/datatables.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/bootstrap-selectpicker.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/bootstrap-notify.js')}}" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-switch/3.3.4/js/bootstrap-switch.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.3.1/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.3.1/js/buttons.bootstrap4.min.js"></script>

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.27/build/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.27/build/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.3.1/js/buttons.html5.min.js"></script>
    <script>
        $(document).ready(function () {
            $(function () {
                $('[data-toggle="tooltip"]').tooltip()
            });
            
            let name = "CMMS Users List " + new Date();

            $('#datatable').DataTable({
                "pagingType": "full_numbers",
                "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                responsive: true,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Find an item",
                },
                "bLengthChange": false,
                dom: 'Blfrtip',
                buttons: [ 
                    {
                        extend: 'excelHtml5',
                        title: name
                    },
                    {
                        extend: 'pdfHtml5',
                        title: name
                    }]
            });

            var table = $('#datatable').DataTable();
        });

        function setActive(user, el){
            var active;
            var id = user.id;

            if(el.checked == 1){
                active = 1;
            }else{
                active = 0;
            }

            var form_data = "user_id="+id+"&active="+active;
            
            $.ajax({
                url : '/api/users/activate',
                method: 'put',
                data : form_data,
                success: function(data, status){
                    if(data.error){
                        presentNotification(data.message, 'danger', 'top', 'right');
                    }else{
                        presentNotification(data.message, 'info', 'top', 'right');
                    }
                },

                error : function(xhr, desc, err){
                    presentNotification('Network error', 'danger', 'top', 'right');
                }
            });
        }

        const editUser = (user) => {
            const keys = Object.keys(user);

            keys.forEach(function(item, index){
               $("#edit_user").find(`[name="${item}"]`).val(user[`${item}`]);
            });

            $(".selectpicker").selectpicker("refresh");

            $("#edit-user-modal").modal("show");
        }

        $("#edit_user").on("submit", function(e){
            e.preventDefault();

            const data = $(this).serialize();
            
            let btn = $(this).find('[type=submit]');
            const id = $(this).find('[name=id]').val();
            
            submit_form("/api/users/edit/"+id, "put", data, undefined, btn, true);
        });

        const deactivateUser = (user) => {
            $("#user_fullname").html(`${user.firstname} ${user.lastname}`);
            $("#deactivate_id").val(user.id);

            $("#deactivate-user-modal").modal("show");
        }
        
        const activateUser = (user) => {
            $("#user_fullname_active").html(`${user.firstname} ${user.lastname}`);
            $("#activate_id").val(user.id);

            $("#activate-user-modal").modal("show");
        }

        const resetPassword = (user) => {
            $("#user_password_reset").html(`${user.firstname} ${user.lastname}`);
            $("#reset_id").val(user.id);

            $("#reset-user-modal").modal("show");
        }

        $("#reset_password").on("submit", function(e) {
            e.preventDefault();

            let btn = $(this).find('[type=submit]');
            const id = $(this).find('[name=id]').val();

            submit_form('/api/users/reset-password/'+id, 'put', null, undefined, btn, true);

        });

        $("#deactivate_user").on("submit", function(e){
            e.preventDefault();

            let btn = $(this).find('[type=submit]');
            const id = $(this).find('[name=id]').val();
            
            submit_form("/api/users/deactivate/"+id, "put", null, undefined, btn, true);
        });

        $("#activate_user").on("submit", function(e){
            e.preventDefault();

            let btn = $(this).find('[type=submit]');
            const id = $(this).find('[name=id]').val();
            
            submit_form("/api/users/activate/"+id, "put", null, undefined, btn, true);
        });
    </script>
    @endsection