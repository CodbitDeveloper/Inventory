@php
$auth_admin = Auth::guard('admin')->user();
@endphp
<div class="sidebar" data-color="blue">
    <div class="sidebar-wrapper">
        <div class="logo">
            <a href="/admin" class="simple-text logo-normal">
                {{Auth::guard("admin")->user()->region->name}}
            </a>
        </div>
        <ul class="nav">
            <li>
                <a href="/admin">
                    <i class="now-ui-icons business_bank"></i>
                    <p>Dashboard</p>
                </a>
            </li>
            @if($auth_admin->role == 'Biomedical Engineer' || $auth_admin->role == 'Admin')
                <li>
                    <a href="/admin/work-orders">
                        <i class="now-ui-icons ui-2_settings-90"></i>
                        <p class="sidebar-normal">Work Orders</p>
                    </a>
                </li>
            @endif
            @if($auth_admin->role == 'Admin')
            <li>
                <a href="/admin/hospitals">
                    <i class="now-ui-icons health_ambulance"></i>
                    <p>Hospitals</p>
                </a>
            </li>
            <li>
                <a href="/admin/engineer-requests">
                    <i class="now-ui-icons ui-1_email-85"></i>
                    <p>Engineer Requests</p>
                </a>
            </li>
            <li>
                <a href="/admin/districts">
                    <i class="now-ui-icons location_pin"></i>
                    <p>Districts</p>
                </a>
            </li>
            <li>
                <a href="/admin/donations">
                    <i class="now-ui-icons shopping_delivery-fast"></i>
                    <p>Donations</p>
                </a>
            </li>
            <li>
                <a href="/admin/equipment">
                    <i class="now-ui-icons ui-2_settings-90"></i>
                    <p>Equipment</p>
                </a>
            </li>
            <li>
                <a href="/admin/categories">
                    <i class="now-ui-icons files_single-copy-04"></i>
                    <p>Categories</p>
                </a>
            </li>
            <li>
                <a href="/admin/users">
                    <i class="now-ui-icons users_single-02"></i>
                    <p>Users and Engineers</p>
                </a>
            </li>
            @endif
        </ul>
    </div>
</div>