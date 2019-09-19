<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/* Route::get('/', function () {
    return view('auth/login');
})->middleware('guest'); */

Route::middleware('guest')->group(function(){
    /*Route::get('/', function(){
        return view('auth/login');
    });*/
    Route::get('/', 'UserController@login')->name('login');
    Route::get('/user/profile-complete/{id}', 'UserController@completeProfile')->name('profile.complete');
    Route::get('/request/guest/{request_link}', 'RequestsController@guestRequest')->name('request.guest');
    Route::get('/admin/profile-complete/{id}', 'AdminController@completeProfile')->name('admin-profile.complete');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/users/logout', 'Auth\LoginController@userLogout')->name('user.logout');

Route::middleware('auth')->group(function(){
    Route::get('/inventory', 'AssetController@index')->name('inventory');
    Route::get('/inventory/add', 'AssetController@create')->name('add-item');
    Route::get('/inventory/{asset}', 'AssetController@show')->name('show-item');
    Route::get('/profile', 'UserController@index')->name('profile');
    Route::get('/users', 'UserController@listAll')->name('users');
    Route::get('/users/add', 'UserController@addNew')->name('users');
    Route::get('/reports', 'ReportController@index')->name('reports');
    Route::get('/schedule', 'ScheduleController@index')->name('schedule');
    Route::get('/schedule/fetch_all', 'ScheduleController@fetchAll')->name('schedule');
    Route::get('/departments', 'DepartmentController@viewALl')->name('departments');
    Route::get('/department/{department}', 'DepartmentController@view')->name('department.view');
    Route::get('/units', 'UnitController@viewAll')->name('units');
    Route::get('/settings', 'SettingController@index')->name('settings');
    Route::get('/vendors', 'ServiceVendorController@index')->name('vendors');
    Route::get('/requests', 'RequestsController@index')->name('request');
    Route::get('/request/add', 'RequestsController@create');
    Route::get('/markAsRead', 'NotificationController@markAllAsRead')->name('mark-as-read');
    Route::get('/categories', 'CategoryController@index')->name('categories');
    Route::get('/spare-parts', 'PartController@index')->name('spare-parts');
    Route::get('/spare-part/{part}', 'PartController@show')->name('spare-part.show');
    Route::get('/purchase-orders', 'PurchaseOrderController@index')->name('purchase-orders');
    Route::get('/purchase-orders/add', 'PurchaseOrderController@create')->name('purchase-order.add');
    Route::get('/purchase-order/{purchaseOrder}', 'PurchaseOrderController@show')->name('purchase-order.show');
    Route::get('/purchase-order/{hash_link}/approval', 'PurchaseOrderController@approval')->name('purchase-order.approval');
    Route::get('/request/{request}', 'RequestsController@show')->name('request.show');
    Route::get('/work-orders', 'WorkOrderController@index')->name('work-orders');
    Route::get('/work-order/{workOrder}', 'WorkOrderController@show')->name('work-order.show');
    Route::get('/work-orders/add', 'WorkOrderController@create')->name('work-order.add');
    Route::get('/pm-schedules', 'PmScheduleController@index')->name('pm-schedules.show');
    Route::get('/pm-schedules/add', 'PmScheduleController@create')->name('pm-schedules.create');
    Route::get('/pm-schedule/record', 'PreventiveMaintenanceController@create')->name('pm.create');
    Route::get('/pm-schedule/{pmSchedule}', 'PmScheduleController@show')->name("pm.show");
    Route::get('/pm-schedule/{pmSchedule}/record', 'PreventiveMaintenanceController@make')->name('pm.make');
    
    Route::get('/part-categories/upload-csv', 'PartCategoryController@uploadCSV');
    Route::get('/asset-categories/upload-csv', 'AssetCategoryController@uploadCSV');
    Route::get('/fault-categories/upload-csv', 'FaultCategoryController@uploadCSV');
    Route::get('/priorities/upload-csv', 'PriorityController@uploadCSV');
    
    Route::get('/download/category-csv', 'FileController@downloadCategoryCSV');
    Route::get('/files/download/{file}', 'FileController@download');
});



    
Route::middleware('admin')->prefix('admin')->group(function(){
    Route::get('/', 'AdminController@index')->name('admin.dashboard');
    Route::get('/profile', 'AdminController@profile')->name('admin.profile');
    Route::get('/hospitals', 'HospitalController@index')->name('admin.hospitals');
    Route::get('/hospitals/{hospital}', 'HospitalController@viewHospital')->name('admin.hospitals.view');
    Route::get('/equipment', 'EquipmentController@index')->name("admin.equipment.view");
    Route::get('/equipment/add', 'EquipmentController@create')->name("admin.equipment.create");
    Route::get('/equipment/{equipment}', 'EquipmentController@show')->name("admin.equipment.show");
    Route::get('/categories', 'AdminCategoryController@index')->name('admin.categories');
    Route::get('/donations', 'DonationController@index')->name("admin.donations");
    Route::get('/donations/add', 'DonationController@create')->name("admin.donations.add");
    Route::get('/donations/{donation}', 'DonationController@show')->name("admin.donations.show");
    Route::get("/districts", "DistrictController@index")->name("admin.districts");
    Route::get('/users', 'AdminController@viewAll')->name('admin.users');
    Route::get('/logout', 'Auth\AdminLoginController@logout')->name('admin.logout');
    Route::get('/equipment-types', 'CategoryController@index')->name('equipment-types');
    Route::get('/engineers', 'AdminController@showEngineers')->name('show-engineers');
    Route::get('/engineers/add', 'AdminController@addEngineer')->name('add-engineer');
    Route::get('/requests', 'RequestsController@adminIndex')->name('requests');
    Route::get('/assigned', 'RequestsController@presentEngineerJobs')->name('assigned');
    Route::get('/assigned/maintenance/{equipment}/{job}', 'RequestsController@handleMaintenance')->name('request.maintenance');
    Route::get('/approve', 'MaintenanceController@adminApprovals')->name('admin-approve');
    Route::get('/markAsRead', 'NotificationController@markAllAsRead')->name('mark.read');
    Route::get('/work-orders', 'WorkOrderController@adminView')->name('work-orders');
});


Route::get('/admin/login', 'Auth\AdminLoginController@showLoginForm')->name('admin.login');
Route::post('/admin/login', 'Auth\AdminLoginController@login')->name('admin.login.submit');
Route::get('/purchase-order/{hashLink}/generate', 'PurchaseOrderController@generatePdf');
