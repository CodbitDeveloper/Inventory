@extends('layouts.user-dashboard', ['page_title' => 'Service Vendors'])
@section('content')
    <div class="content">
        <div class="row">
            <div class="col-md-12 center">
                <div class="card">
                    <div class="card-header">
                        <h4 class="inline-block">Service Vendors List</h4>
                    </div>
                    <div class="card-body">
                        <table id="datatable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Contact Name</th>
                                    <th>Contact Number</th>
                                    <th>Email</th>
                                    <th>Vendor Type</th>
                                    <th>Address</th>
                                    <th>Website</th>
                                    @if($user->role == 'Admin')
                                        <th class="disabled-sorting">Actions</th>
                                    @endif
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th>Name</th>
                                    <th>Contact Name</th>
                                    <th>Contact Number</th>
                                    <th>Email</th>
                                    <th>Vendor Type</th>
                                    <th>Address</th>
                                    <th>Website</th>
                                    @if($user->role == 'Admin')
                                        <th class="disabled-sorting text-right">Actions</th>
                                    @endif
                                </tr>
                            </tfoot>
                            <tbody>
                            @foreach($vendors as $vendor)
                                <tr>
                                    <td><b>{{$vendor->name}}</b></td>
                                    <td>{{$vendor->contact_name !== null ? $vendor->contact_name : 'N/A'}}</td>
                                    <td>{{$vendor->contact_number !== null ? $vendor->contact_number : 'N/A'}}</td>
                                    <td>{{$vendor->email !== null ? $vendor->email : 'N/A'}}</td>
                                    <td>{{$vendor->vendor_type !== null ? $vendor->vendor_type : 'N/A'}}</td>
                                    <td>{{$vendor->address !== null ? $vendor->address : 'N/A'}}</td>
                                    <td>{{$vendor->website !== null ? $vendor->website : 'N/A'}}</td>
                                    @if($user->role == 'Admin')
                                        <td>
                                            <a href="javascript:void(0)" class="edit" data-placement="left" title="Edit" data-toggle="tooltip" onclick = "edit({{$vendor}})">
                                                <i class="fas fa-pen text-muted"></i>
                                            </a>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="addVendorModal" tabindex="-1" role="dialog" aria-labelledby="addVendorLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
        <form method = "post" id="add_new_vendor_form">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;<span class="sr-only">Close</span></button>
                <h6 class="header">New Vendor</h6>
            </div>
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group col-md-12">
                        <label><b>Vendor Name</b> <span class="text-danger">*</span></label>
                        <input type="text" class="form-control resetable" name="name" required/>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label><b>Contact Person Name</b></label>
                        <input type="text" class="form-control resetable" name="contact_name">
                    </div>
                    <div class="form-group col-md-6">
                        <label><b>Contact Number</b></label>
                        <input type="tel" class="form-control resetable" name="contact_number">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label><b>Email</b></label>
                        <input type="email" class="form-control resetable" name="email">
                    </div>
                    <div class="form-group col-md-6">
                        <label><b>Website</b></label>
                        <input type="url" class="form-control resetable" name="website">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label><b>Vendor Type</b> <span class="text-danger">*</span></label>
                        <select class="selectpicker col-md-12" title="Vendor Type" data-style="btn btn-purple" name="vendor_type" required>
                            <option>Maintenance Contractors</option>
                            <option>Purchase equipment</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label><b>Address</b></label>
                        <input type="text" class="form-control resetable" name="address">
                    </div>
                </div>
            </div>
            <div class="modal-footer mt-4">
                <button type="submit" class="btn btn-purple float-right" id="btn_submit">Save</button>
            </div>
        </div>
        </form>
    </div>
    </div>
    <div class="modal fade" id="editVendorModal" tabindex="-1" role="dialog" aria-labelledby="editVendorLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
        <form method = "post" id="edit_new_vendor_form">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;<span class="sr-only">Close</span></button>
                <h6 class="header">Edit Vendor</h6>
            </div>
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group col-md-12">
                        <label><b>Vendor Name</b> <span class="text-danger">*</span></label>
                        <input type="text" class="form-control resetable" name="name" required/>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label><b>Contact Person Name</b></label>
                        <input type="text" class="form-control resetable" name="contact_name">
                    </div>
                    <div class="form-group col-md-6">
                        <label><b>Contact Number</b></label>
                        <input type="tel" class="form-control resetable" name="contact_number">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label><b>Email</b></label>
                        <input type="email" class="form-control resetable" name="email">
                    </div>
                    <div class="form-group col-md-6">
                        <label><b>Website</b></label>
                        <input type="url" class="form-control resetable" name="website">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label><b>Vendor Type</b> <span class="text-danger">*</span></label>
                        <select class="selectpicker col-md-12" title="Vendor Type" data-style="btn btn-purple" name="vendor_type" required>
                            <option>Maintenance Contractors</option>
                            <option>Purchase equipment</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label><b>Address</b></label>
                        <input type="text" class="form-control resetable" name="address">
                    </div>
                </div>
            </div>
            <div class="modal-footer mt-4">
                <button type="submit" class="pull-right btn btn-purple btn-fill btn-wd" id="btn_edit">Update</button>
            </div>
        </div>
    </form>
        </div>
    </div>
    @if($user->role == 'Admin')
        <a href="javascript:void(0)" data-toggle="modal" data-target="#addVendorModal">
            <div class="fab">
                <i class="fas fa-plus"></i>
            </div>
        </a>
    @endif
@endsection
@section('scripts')
    <script src="{{asset('js/datatables.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/bootstrap-notify.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/bootstrap-selectpicker.js')}}" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-switch/3.3.4/js/bootstrap-switch.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.3.1/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.3.1/js/buttons.bootstrap4.min.js"></script>

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.27/build/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.27/build/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.3.1/js/buttons.html5.min.js"></script>
    <script>
        let temp_edit_id;
        $(document).ready(function () {
            
            $(function () {
                $('[data-toggle="tooltip"]').tooltip()
            });

            let table = generateExportDtbl('#datatable', undefined, "Search for a vendor");
        });

        $('#add_new_vendor_form').on('submit', function(e){
            e.preventDefault();
            
            let btn = $('#btn_submit');
            let data = $(this).serialize() + "&hospital_id={{$user->hospital_id}}";

            submit_form("/api/vendors/add", "post", data, undefined, btn, true);
        });
        
        $('#editVendorModal').on('hide.bs.modal', function(e){
            $('#btn_edit').prop('disabled', true);
        });

        $('#edit_new_vendor_form input').on('input', function(){
            $('#btn_edit').prop('disabled', false);
        });

        let edit = (vendor) => {
            temp_edit_id = vendor.id;
            
            $.each(Object.keys(vendor), function(index, key){
                $("#edit_new_vendor_form").find(`[name="${key}"]`).val(vendor[`${key}`]);
            });

            $("#edit_new_vendor_form").find(`[name="vendor_type"]`).selectpicker("refresh");
            
            $("#editVendorModal").modal("show");
        }

        $("#edit_new_vendor_form").on("submit", function(e){
            e.preventDefault();

            let data  = $(this).serialize();
            let btn = $(this).find('[type="submit"]');

            submit_form("/api/vendors/update/"+temp_edit_id, "put", data, undefined, btn, true);
        });
    </script>
@endsection