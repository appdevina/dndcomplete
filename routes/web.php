<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DailyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MonthlyController;
use App\Http\Controllers\OverOpenController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\WeeklyController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('login.index');
})->name('/');

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth')->group(
    function () {
        ##DASHBOARD
        Route::get('dashboard', [DashboardController::class, 'index']);
        ##LOGOUT
        Route::post('/logout', [AuthController::class, 'logout']);

        ##DAILY
        Route::get('/daily', [DailyController::class, 'indexUser'])->name('dailyuser');
        Route::post('/daily', [DailyController::class, 'store']);
        Route::post('/daily/change', [DailyController::class, 'change']);
        Route::post('/daily/delete', [DailyController::class, 'destroy']);
        Route::post('/daily/edit/', [DailyController::class, 'update']);
        Route::get('/daily/edit/{id}', [DailyController::class, 'edituser']);
        Route::get('/daily/template', [DailyController::class, 'templateUser']);
        Route::post('/daily/import', [DailyController::class, 'importDailyUser']);
        ##WEEKLY
        Route::get('/weekly', [WeeklyController::class, 'indexUser']);
        Route::post('/weekly', [WeeklyController::class, 'store']);
        Route::get('/weekly/template', [WeeklyController::class, 'templateUser']);
        Route::post('/weekly/import', [WeeklyController::class, 'importWeeklyUser']);
        Route::post('/weekly/change', [WeeklyController::class, 'change']);
        Route::get('/weekly/change/result/{id}', [WeeklyController::class, 'showresult']);
        Route::post('/weekly/delete', [WeeklyController::class, 'destroy']);
        Route::post('/weekly/update/{id}', [WeeklyController::class, 'update']);
        Route::get('/weekly/edit/{id}', [WeeklyController::class, 'edit']);
        ##MONTHLY
        Route::get('/monthly/template', [MonthlyController::class, 'templateUser']);
        Route::post('/monthly/import', [MonthlyController::class, 'importMonthlyUser']);
        Route::get('/monthly', [MonthlyController::class, 'indexUser']);
        Route::post('/monthly', [MonthlyController::class, 'store']);
        Route::post('/monthly/change', [MonthlyController::class, 'change']);
        Route::get('/monthly/change/result/{id}', [MonthlyController::class, 'showresult']);
        Route::post('/monthly/delete', [MonthlyController::class, 'destroy']);
        Route::post('/monthly/update/{id}', [MonthlyController::class, 'update']);
        Route::get('/monthly/edit/{id}', [MonthlyController::class, 'edit']);
        ##REPORT
        Route::get('/result', [ReportController::class, 'indexuser']);
        ##REQUEST CHANGE TASK
        Route::get('/request', [DashboardController::class, 'request']);
        Route::post('/request', [DashboardController::class, 'submit']);
        Route::get('/request/create', [DashboardController::class, 'requestcreate']);
        Route::get('/request/cancel/{id}', [DashboardController::class, 'cancel']);

        ##APPROVAL
        Route::get('/req', [DashboardController::class, 'reqindex']);
        Route::get('/req/exist', [DashboardController::class, 'exist']);
        Route::get('/req/replace', [DashboardController::class, 'replace']);
        Route::get('/req/approve/{id}', [DashboardController::class, 'approve']);
        Route::get('/req/reject/{id}', [DashboardController::class, 'reject']);



        ##ROUTE ADMIN
        Route::middleware('isAdmin')->group(function () {
            ##USER
            Route::get('user', [UserController::class, 'index']);
            Route::post('user', [UserController::class, 'store']);
            Route::post('user/update/{id}', [UserController::class, 'update']);
            Route::get('user/delete/{id}', [UserController::class, 'destroy']);
            Route::get('user/active/{id}', [UserController::class, 'active']);
            Route::get('user/create', [UserController::class, 'create']);
            Route::get('user/export', [UserController::class, 'export']);
            Route::get('user/template', [UserController::class, 'template']);
            Route::post('user/import', [UserController::class, 'import']);
            Route::get('user/{id}', [UserController::class, 'edit']);

            ##DAILY ADMIN
            Route::get('admin/daily', [DailyController::class, 'index']);
            Route::post('admin/daily/export', [DailyController::class, 'exportAdmin']);
            Route::post('admin/daily/import', [DailyController::class, 'importDailyUser']);
            Route::get('admin/daily/{id}', [DailyController::class, 'edit']);
            Route::post('admin/daily/{id}', [DailyController::class, 'update']);
            ##WEEKLY ADMIN
            Route::get('admin/weekly', [WeeklyController::class, 'index']);
            Route::post('admin/weekly/export', [WeeklyController::class, 'exportAdmin']);
            Route::post('admin/weekly/import', [WeeklyController::class, 'importWeeklyUser']);
            ##MONTLY ADMIN
            Route::get('admin/monthly', [MonthlyController::class, 'index']);
            Route::post('admin/monthly/export', [MonthlyController::class, 'exportAdmin']);
            Route::post('admin/monthly/report', [MonthlyController::class, 'reportAdmin']);

            ##REPORT
            Route::get('/admin/report', [ReportController::class, 'index']);
            Route::post('/admin/report/export', [ReportController::class, 'exportIndividu']);

            ##OVEROPEN
            Route::get('/admin/overopen', [OverOpenController::class, 'index']);
            Route::post('/admin/overopen', [OverOpenController::class, 'store']);
            Route::get('/admin/overopen/create', [OverOpenController::class, 'create']);
            Route::post('/admin/overopen/export', [OverOpenController::class, 'show']);
            Route::post('/admin/overopen/delete', [OverOpenController::class, 'destroy']);


            ##SETTING
            //ROLE
            Route::get('setting/role', [SettingController::class, 'role']);
            Route::post('setting/role', [SettingController::class, 'roleadd']);
            // Route::get('setting/role/{id}', [SettingController::class, 'roleedit']);

            //DIVISI
            Route::get('setting/divisi', [SettingController::class, 'divisi']);
            Route::post('setting/divisi', [SettingController::class, 'divadd']);
            // Route::get('setting/divisi/{id}', [SettingController::class, 'divedit']);

            //AREA
            Route::get('setting/area', [SettingController::class, 'area']);
            Route::post('setting/area', [SettingController::class, 'areaadd']);
            // Route::get('setting/area/{id}', [SettingController::class, 'areaedit']);

            ##BROADCAST
            Route::post('broadcast', [DashboardController::class, 'broadcast']);
        });
    }
);

##HELPER
Route::get('divisi/get/{id}', [UserController::class, 'getdivisi']);
Route::get('approval/get', [UserController::class, 'getapproval']);
Route::get('download/app', [SettingController::class, 'download']);
Route::get('daily/get', [DailyController::class, 'getdaily']);
Route::get('weekly/get', [WeeklyController::class, 'getweekly']);
Route::get('monthly/get', [MonthlyController::class, 'getmonthly']);
