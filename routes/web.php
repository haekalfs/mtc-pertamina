<?php

use App\Http\Controllers\AkhlakController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentPositionController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\KpiController;
use App\Http\Controllers\ManageAccessController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect('/dashboard');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

//External User Registration
Route::get('/user-registration', [RegistrationController::class, 'index'])->name('ext.register.user');

Route::middleware('auth')->group(function () {
    //KPI
    Route::get('/key-performance-indicators', [KpiController::class, 'index'])->name('kpi');
    Route::get('/key-performance-indicators/manage-items', [KpiController::class, 'manage'])->name('manage-kpi');
    Route::get('/key-performance-indicators/preview/{id}', [KpiController::class, 'preview'])->name('preview-kpi');
    Route::get('/key-performance-indicators/report', [KpiController::class, 'report'])->name('report-kpi');
    Route::post('/kpi-store', [KpiController::class, 'store'])->name('kpis.store');
    Route::post('/pencapaian-kpi/store/{kpi}', [KpiController::class, 'store_pencapaian'])->name('pencapaian.kpi.store');

    //Pencapaian AKhlak
    Route::get('/akhlak-achievements/{yearSelected?}', [AkhlakController::class, 'index'])->name('akhlak.achievements');
    Route::get('/akhlak-report', [AkhlakController::class, 'report'])->name('report-akhlak');
    Route::post('/akhlak-store', [AkhlakController::class, 'store'])->name('akhlak.store');

    //Finance
    Route::get('/finances', [FinanceController::class, 'index'])->name('finance');
    Route::get('/finances/manage-items', [FinanceController::class, 'manage'])->name('manage-finance');
    Route::get('/finances/report', [FinanceController::class, 'report'])->name('finance-report');
    Route::post('/finances-store', [FinanceController::class, 'store'])->name('finance.store');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    //Manage Users
    Route::get('/manage-users', [UserController::class, 'manage'])->name('manage.users');
    Route::get('/manage-users/registration', [UserController::class, 'register'])->name('register.users');
    Route::post('/register/user', [UserController::class, 'store'])->name('register.user');

    //manage roles
    Route::get('/manage-roles', [RolesController::class, 'index'])->name('manage.roles');
    Route::post('/post-new-roles', [RolesController::class, 'store'])->name('roles.store');
    Route::delete('/delete-roles/{id}', [RolesController::class, 'destroy'])->name('roles.destroy');

    //manage dept. post.
    Route::get('/manage-department-position', [DepartmentPositionController::class, 'index'])->name('manage.dept.post');
    Route::post('/departments-post', [DepartmentPositionController::class, 'department_store'])->name('department.store');
    Route::post('/positions-post', [DepartmentPositionController::class, 'position_store'])->name('position.store');
    Route::delete('/delete-position/{id}', [DepartmentPositionController::class, 'delete_position'])->name('position.destroy');
    Route::delete('/delete-department/{id}', [DepartmentPositionController::class, 'delete_department'])->name('department.destroy');


    //manage access
    Route::get('/manage-access', [ManageAccessController::class, 'index'])->name('manage.access');
    Route::post('/assign-role-to-page', [ManageAccessController::class, 'grant_access_to_roles'])->name('assign.roles.to.page');
    Route::get('/reset-access/{id}', [ManageAccessController::class, 'remove_access'])->name('remove.access');
});

require __DIR__.'/auth.php';
