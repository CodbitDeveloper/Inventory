<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('user/complete-profile', 'UserController@complete');
Route::post('request/guest/add', 'RequestsController@guestAdd');

Route::post('app/login', 'Auth\LoginController@mobileLogin');

Route::middleware('auth:api')->group(function(){
    Route::get("home/get-upcoming", "HomeController@loadUpcoming");
    Route::get('users/', 'UserController@index');
    Route::post('users/add_user', 'UserController@store');
    Route::get('users/{user}', 'UserController@show');
    Route::put('users/update/{user}', 'UserController@update');
    Route::put('users/edit/{user}', 'UserController@editUser');
    Route::put('users/deactivate/{user}', 'UserController@deactivate');
    Route::put('users/activate/{user}', 'UserController@activate');
    Route::put('users/reset-password/{user}', 'UserController@resetPassword');
    Route::post('users/user-login', 'UserController@userLogin');

    Route::post('asset-category/add', 'AssetCategoryController@store');
    Route::post('fault-category/add', 'FaultCategoryController@store');
    Route::post('priority/add', 'PriorityController@store');
    Route::post('part-category/add', 'PartCategoryController@store');

    Route::put('asset-category/update/{assetCategory}', 'AssetCategoryController@update');
    Route::put('fault-category/update/{faultCategory}', 'FaultCategoryController@update');
    Route::put('part-category/update/{partCategory}', 'PartCategoryController@update');
    Route::put('priority/update/{priority}', 'PriorityController@update');

    Route::post('asset-category/bulk-save', 'AssetCategoryController@bulkSave');
    Route::post('part-category/bulk-save', 'PartCategoryController@bulkSave');
    Route::post('fault-category/bulk-save', 'FaultCategoryController@bulkSave');
    Route::post('priority/bulk-save', 'PriorityController@bulkSave');

    Route::delete('asset-category/delete/{assetCategory}', 'AssetCategoryController@destroy');
    Route::delete('fault-category/delete/{faultCategory}', 'FaultCategoryController@destroy');
    Route::delete('part-category/delete/{partCategory}', 'PartCategoryController@destroy');
    Route::delete('priority/delete/{priority}', 'PriorityController@destroy');

    Route::post('spare-part/add', 'PartController@store');
    Route::put('spare-part/update/{part}', 'PartController@update');
    
    Route::get('assets/{hospital}', 'AssetController@get');
    Route::post('asset/add', 'AssetController@store');
    Route::put('asset/{asset}/update', 'AssetController@update');
    Route::delete('asset/{asset}/delete', 'AssetController@destroy');
    Route::get('asset/{asset}/get-parts', 'AssetController@getParts');
    Route::get('asset/{asset}/get-files', 'AssetController@getFiles');
    Route::get('asset/{asset}/get-children', 'AssetController@getChildren');
    Route::get('asset/{asset}/depreciation', 'AssetController@depreciation');
    Route::post('asset/{asset}/toggle', 'AssetController@toggle');
    Route::post('asset/{asset}/remove-part', 'AssetController@removePart');
    Route::post('asset/{asset}/assign-parts', 'AssetController@assignPart');
    Route::post('asset/{asset}/remove-child', 'AssetController@removeChild');
    Route::post('asset/{asset}/assign-children', 'AssetController@assignChild');

    Route::post("purchase-order/add", 'PurchaseOrderController@store');
    Route::post("purchase-order/{purchaseOrder}/update", 'PurchaseOrderController@update');
    Route::post("purchase-order/{purchaseOrder}/send", 'PurchaseOrderController@sendLink');
    Route::post("purchase-order/{purchaseOrder}/approve", 'PurchaseOrderController@approve');
    Route::post("purchase-order/{purchaseOrder}/decline", 'PurchaseOrderController@decline');
    Route::post("purchase-order/{purchaseOrder}/fulfill", 'PurchaseOrderController@fulfill');
    
    Route::post('file/add', 'FileController@store');
    
    Route::get('equipment/{equipment}', 'EquipmentController@show');
    Route::put('equipment/update/{equipment}', 'EquipmentController@update');

    Route::post('departments/add', 'DepartmentController@store');
    Route::put('departments/{department}/update', 'DepartmentController@update');

    Route::post('units/add', 'UnitController@store');
    Route::put('units/{unit}/update', 'UnitController@update');

    Route::post('requests/add', 'RequestsController@store');
    Route::put('request/{work_request}/decline', 'RequestsController@decline');
    Route::put('request/{work_request}/approve', 'RequestsController@approve');
    Route::put('request/{work_request}/update', 'RequestsController@update');

    Route::post("work-order/add", "WorkOrderController@store");
    Route::put("work-order/{workOrder}/update", "WorkOrderController@update");
    Route::get("work-order/available-technicians/{workOrder}", "WorkOrderController@availableTechnicians");
    Route::post("work-order/{workOrder}/assign-team", "WorkOrderController@assignTeam");
    Route::post("work-order/{workOrder}/record-activity", "WorkOrderController@recordActivity");
    Route::post("work-order/{workOrder}/comment", "WorkOrderController@comment");
    Route::get("work-order/{workOrder}/comments", "WorkOrderController@getComments");
    Route::put("work-order/{workOrder}/assign-asset", "WorkOrderController@assignAsset");
    Route::get("work-order/{workOrder}/activities", "WorkOrderController@getActivities");
    Route::get("work-order/{workOrder}/spare-parts", "WorkOrderController@getSpareParts");
    Route::post("work-order/{workOrder}/add-part", "WorkOrderController@attachSpareParts");
    Route::post("work-order/{workOrder}/update-status", "WorkOrderController@updateStatus");
    Route::post("work-order/{workOrder}/complete", "WorkOrderController@complete");
    Route::post("work-order/{workOrder}/save-report", "WorkOrderController@report");

    Route::post("pm-schedule/add", 'PmScheduleController@store');
    Route::put('pm-schedule/update/{pmSchedule}', 'PmScheduleController@update');
    Route::post("pm-schedule/{pmSchedule}/task/add", "PmScheduleController@addTask");
    Route::delete("pm-schedule/{pmAction}/task/delete", "PmScheduleController@deleteTask");
    
    Route::post("pm/add", "PreventiveMaintenanceController@store");
    Route::post("pm/{preventiveMaintenance}/approve", "PreventiveMaintenanceController@approve");
    Route::post("pm/{preventiveMaintenance}/decline", "PreventiveMaintenanceController@decline");
    
    Route::post('schedule/add', 'ScheduleController@store');

    Route::post('settings/{hospital_id}/generate-link', 'SettingController@generateRequestLink');
    Route::post('settings/{hospital_id}/send-link', 'SettingController@sendLink');

    Route::post('vendors/add', 'ServiceVendorController@store');
    Route::put('vendors/update/{vendor}', 'ServiceVendorController@update');

    Route::get("reports/work-orders/index", "ReportController@workOrderIndex");
    Route::get("reports/work-orders/status", "ReportController@workOrderByStatus");
    Route::get("reports/work-orders/department", "ReportController@workOrderByDepartment");
    Route::get("reports/work-orders/unit", "ReportController@workOrderByUnit");
    Route::get("reports/work-orders/approval", "ReportController@workOrderByApproval");
    Route::get("reports/preventive-maintenance", "ReportController@getPms");
    Route::get("reports/get-months", "ReportController@getMonths");
    Route::get("reports/get-pm-months", "ReportController@getPmMonths");
    Route::get("reports/get-years", "ReportController@getYears");
    Route::get("reports/get-pm-years", "ReportController@getPmYears");
    Route::get("reports/equipment", "ReportController@equipmentReport");
    Route::get("reports/technicians", "ReportController@technicianReport");

    Route::post('request-engineer/add', 'RequestEngineerController@store');
});

Route::post('admin/complete-profile', 'AdminController@complete');

Route::middleware('passport:admin-api')->group(function(){
    Route::get('admins/', 'AdminController@index');
    Route::post('admins/add_admin', 'AdminController@store');
    Route::get('admins/{admin}', 'AdminController@show');
    Route::put('admins/update/{admin}', 'AdminController@update');
    Route::post('admins/admin-login', 'Auth\AdminLoginController@adminLogin');
    Route::put('admins/activate/{admin}', 'AdminController@activate');
    Route::put('admins/deactivate/{admin}', 'AdminController@deactivate');
    Route::put('admins/reset-password/{admin}', 'AdminController@resetPassword');

    Route::get('hospitals/', 'HospitalController@index');
    Route::get('hospitals/{hospital}/get-equipment', 'HospitalController@getEquipment');
    Route::get('hospitals/{hospital}/get-departments', 'HospitalController@getDepartments');
    Route::get('hospitals/{hospital}', 'HospitalController@show');

    Route::post("categories/add", "AdminCategoryController@store");
    Route::put("categories/update/{adminCategory}", "AdminCategoryController@update");
    Route::delete("categories/delete/{adminCategory}", "AdminCategoryController@destroy");

    Route::post("equipment/add", "EquipmentController@store");
    Route::put("equipment/{equipment}/update", "EquipmentController@update");
    Route::put("equipment/{equipment}/update-status", "EquipmentController@updateStatus");

    Route::post("donations/add", "DonationController@store");

    Route::get("districts/{district}/view-hospitals", "DistrictController@viewHospitals");
});

// route for issue tokens
Route::group(['middleware' => ['passport:admin-api']], function() {
    Route::post('/token1', '\Laravel\Passport\Http\Controllers\AccessTokenController@issueToken');
});

Route::group(['middleware' => ['cors']], function() {
    Route::get("app/work-orders", "WorkOrderController@appGet");
    Route::get("app/work-orders/actions/{workOrder}", "WorkOrderController@appActionsGet");
    Route::get("app/work-orders/comments/{workOrder}", "WorkOrderController@appCommentsGet");
    Route::post("app/work-order/{workOrder}/record-activity", "WorkOrderController@recordActivity");
    Route::post("app/work-order/{workOrder}/comment", "WorkOrderController@comment");
    Route::get("app/pm-schedules", "PmScheduleController@appGetPmSchedules");
    Route::get("app/{pmSchedule}/preventive-maintenances", "PmScheduleController@appGetPmScheduleHistory");
    Route::post("app/pm/add", "PreventiveMaintenanceController@store");
    Route::get("app/equipment/search", "AssetController@search");
});