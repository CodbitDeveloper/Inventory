@extends('layouts.user-dashboard', ['page_title' => $part->name])
@section('styles')
<style>
    .image img{
        object-fit: cover;
    }

    .no-border{
        font-size: 24px;
        font-weight: bolder;
    }

    .text-small{
        font-size: 12px;
    }
</style>
@endsection
@section('content')
<div class="content">
    <div class="row">
        <div class="col-md-3">
            <div class="card card-user">
                <div class="">
                    <img src="{{asset('img/assets/parts/'.$part->image)}}" onerror = "this.src = '{{asset('img/image_placeholder.jpg')}}'"/>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <h5 class="title pb-2">{{$part->name}}</h5>
                    <ul class="nav nav-tabs nav-tabs-primary text-center mt-2" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#details" role="tablist">
                                Part Details
                            </a>
                        </li>
                        @if($user->role == 'Admin' || $user->role == 'Regular Technician')
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#assets" role="tablist">
                                    Associated Assets
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#work-orders" role="tablist">
                                    Work Orders
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#edit" role="tablist">
                                    Edit Part
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
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="description-label">Quantity In Stock</label>
                                                <p class="no-border">{{$part->quantity != null ? $part->quantity : 'N/A'}}  @if($part->quantity <= $part->min_quantity) <span class="text-small text-danger"><b>Requires restock</b></span>@endif</p>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="description-label">Unit Price</label>
                                                <p class="no-border">$ {{$part->cost != null ? $part->cost : 'N/A'}}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="description-label">Category</label>
                                                <p class="">{{$part->part_category != null ? $part->part_category->name : 'N/A'}}</p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="description-label">Manufacture Year</label>
                                                <p class="">{{$part->manufacturer_year != null ? Carbon\Carbon::parse($part->manufacturer_year)->format('Y') : 'N/A'}}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="description-label">Description</label>
                                                <p class="">{{$part->description != null ? $part->description : 'No description for this part yet'}}</p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="description-label">Location</label>
                                                <p class="">{{$part->area != null ? $part->area : 'N/A'}}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="assets">
                            <div class="row">
                                <table class="table table-bordered" id="assets_table">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Equipment Number</th>
                                            <th>Status</th>
                                            <th>Availability</th>
                                            <th>Date Created</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>Name</th>
                                            <th>Equipment Number</th>
                                            <th>Status</th>
                                            <th>Availability</th>
                                            <th>Date Created</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                    @foreach($part->assets as $asset)
                                        <tr>
                                            <td>{{$asset->name}}</td>
                                            <td>{{$asset->asset_code}}</td>
                                            <td>{{$asset->status}}</td>
                                            <td>{{$asset->availability}}</td>
                                            <td>{{Carbon\Carbon::parse($asset->created_at)->format('j F, Y')}}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane" id="work-orders">
                            <div class="row">
                                <table class="table table-bordered" id="work_orders">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>#</th>
                                            <th>Due Date</th>
                                            <th>Status</th>
                                            <th>Last Updated</th>
                                            <th>Created</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>Title</th>
                                            <th>#</th>
                                            <th>Due Date</th>
                                            <th>Status</th>
                                            <th>Last Updated</th>
                                            <th>Created</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                    @foreach($part->work_orders as $work_order)
                                        <tr>
                                            <td><a href="/work-order/{{$work_order->id}}"><b>{{$work_order->title}}</b></a></td>
                                            <td>{{$work_order->wo_number}}</td>
                                            <td>{{$work_order->due_date != null ? date('jS F, Y', strtotime($work_order->due_date)) : 'N/A'}}</td>
                                            <td>
                                                @if($work_order->status == 1)
                                                <span class="badge badge-light">Closed</span>
                                                @elseif($work_order->status == 2)
                                                <span class="badge badge-success">In Progress</span>
                                                @elseif($work_order->status == 3)
                                                <span class="badge badge-primary">On Hold</span>
                                                @elseif($work_order->status == 4)
                                                <span class="badge badge-info">Open</span>
                                                @elseif($work_order->status == 5)
                                                <span class="badge badge-warning">Pending</span>
                                                @endif
                                            </td>
                                            <td>{{Carbon\Carbon::parse($work_order->updated_at)->format('jS F, Y')}}</td>
                                            <td>{{Carbon\Carbon::parse($work_order->created_at)->format('jS F, Y')}}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane" id="edit">
                            <form id="edit_part">
                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label><b>Part Name</b> <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control resetable" name="name" value="{{$part->name}}" required/>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label><b>Unit Price</b> <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" class="form-control resetable" name="cost" value="{{$part->cost}}" required>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label><b>Quantity In Stock</b></label>
                                        <input type="number" step="1" class="form-control resetable" value="{{$part->quantity}}" name="quantity">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label><b>Minimum Quantity</b> <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" class="form-control resetable" name="min_quantity" value="{{$part->min_quantity}}" required>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-4 col-sm-12">
                                        <label><b>Year of Manufacture</b></label>
                                        <input type="text" class="form-control resetable" name="manufacturer_year" value="{{Carbon\Carbon::parse($part->manufacturer_year)->format('Y')}}"/>
                                    </div>
                                    <div class="form-group col-md-4 col-sm-12">
                                        <label><b>Location</b></label>
                                        <input type="text" class="form-control resetable" name="area" value="{{$part->area}}">
                                    </div>
                                    <div class="form-group col-md-4 col-sm-12">
                                        <label><b>Category</b></label>
                                        <select class="selectpicker col-sm-12" title="Category" data-style="btn btn-purple" name="part_category_id" required>
                                            @foreach($part_categories as $category)
                                                <option value="{{$category->id}}" <?php if($part->part_category_id == $category->id){echo 'selected';}?>>{{$category->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-row">
                                    
                                    <div class="form-group col-md-6 mt-4 col-sm-12">
                                        <label><b>Description</b></label>
                                        <textarea class="form-control resetable" name="description" rows="3">{{$part->description}}</textarea>
                                        <button type="submit" class="btn btn-purple mt-5" id="btn_submit">Save</button>
                                    </div>

                                    <div class="fileinput fileinput-new col-md-6 col-sm-12" data-provides="fileinput">
                                        <div class="col-md-12 form-group">
                                                <label style="display:block;"><b>Image</b></label>
                                                <div class="fileinput fileinput-new text-center" data-provides="fileinput">
                                                    <div class="fileinput-new thumbnail">
                                                        <img src="{{asset('img/assets/parts/'.$part->image)}}"/>
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
@endsection
@section('scripts')
    <script src="{{asset('js/qrcode.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/bootstrap-selectpicker.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/bootstrap-notify.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/jasny-bootstrap.min.js')}}"></script>
    <script src="{{asset('js/datatables.js')}}" type="text/javascript"></script>
    <script>
        $(document).ready(function(){
            let qr = new QRCode(document.getElementById("qrcode"), "{{$part->id}}");
            let work_orders = generateDtbl("#work_orders", "No work orders require this spare part");
            let assets = generateDtbl("#assets_table", "No work assets associated with this spare part");
        });

        function printContent(el){
            var restorepage = $('body').html();
            var printcontent = $('#' + el).clone();
            $('body').empty().html(printcontent);
            window.print();
            window.location.reload();
        }

        $("#edit_part").on("submit", function(e){
            e.preventDefault();

            let data = new FormData(this);
            
            data.append("hospital_id", '{{Auth::user()->hospital_id}}');
            data.append("_method", "put");

            let btn = $(this).find('[type="submit"]');
            submit_file_form("/api/spare-part/update/{{$part->id}}", "post", data, undefined, btn, true);
        });
    </script>
@endsection