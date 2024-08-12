<?php

use App\Http\Controllers\AgreementController;
use App\Http\Controllers\AkhlakController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentPositionController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\InstagramController;
use App\Http\Controllers\InventoryToolController;
use App\Http\Controllers\KpiController;
use App\Http\Controllers\ManageAccessController;
use App\Http\Controllers\MarketingController;
use App\Http\Controllers\MyProfileController;
use App\Http\Controllers\OperationController;
use App\Http\Controllers\PDController;
use App\Http\Controllers\PenlatController;
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


Route::middleware('auth')->group(function () {
    //External User Registration
    Route::get('/user-registration', [RegistrationController::class, 'index'])->name('ext.register.user');
    //KPI
    Route::get('/key-performance-indicators/index/{yearSelected?}', [KpiController::class, 'index'])->name('kpi');
    Route::post('/kpi-store', [KpiController::class, 'store'])->name('kpis.store');
    Route::post('/pencapaian-kpi/store/{kpi}', [KpiController::class, 'store_pencapaian'])->name('pencapaian.kpi.store');
    Route::get('/key-performance-indicators/achievements/{id}', [KpiController::class, 'pencapaian'])->name('pencapaian-kpi');
    Route::delete('/delete-kpi/{id}', [KpiController::class, 'destroy'])->name('kpi.destroy');
    Route::delete('/delete-pencapaian-kpi/{id}', [KpiController::class, 'destroy_pencapaian'])->name('pencapaian.kpi.destroy');
    //KPI Report
    Route::get('/key-performance-indicators/report/{kpiSelected?}/{periodeSelected?}', [KpiController::class, 'report'])->name('report-kpi');
    //KPI Unused
    Route::get('/key-performance-indicators/manage-items', [KpiController::class, 'manage'])->name('manage-kpi');
    Route::get('/key-performance-indicators/preview/{id}', [KpiController::class, 'preview'])->name('preview-kpi');

    //Pencapaian AKhlak
    Route::get('/akhlak-achievements', [AkhlakController::class, 'index'])->name('akhlak.achievements');
    Route::get('/akhlak-report', [AkhlakController::class, 'report'])->name('report-akhlak');
    Route::post('/akhlak-store/{userSelected?}/{akhlakSelected?}/{periodeSelected?}', [AkhlakController::class, 'store'])->name('akhlak.store');
    Route::get('/akhlak-print/{userSelected}/{akhlakSelected}/{periodeSelected}', [AkhlakController::class, 'print'])->name('akhlak.print');

    //Operation
    Route::get('/operation', [OperationController::class, 'index'])->name('operation');

    //penlat
    Route::get('/penlat/list-pelatihan', [PenlatController::class, 'index'])->name('penlat');
    Route::post('/store-penlat', [PenlatController::class, 'store'])->name('penlat.store');
    Route::get('/penlat/{id}/edit', [PenlatController::class, 'edit'])->name('penlat.edit');
    Route::put('/penlat-update/{id}', [PenlatController::class, 'update'])->name('penlat.update');

    //participant-infographics
    Route::get('/operation/participant-infographics', [OperationController::class, 'participant_infographics'])->name('participant-infographics');
    Route::get('/operation/participant-infographics/import-page', [OperationController::class, 'participant_infographics_import_page'])->name('participant-infographics-import-page');
    Route::post('/import-infografis-peserta', [ImportController::class, 'import'])->name('infografis_peserta.import');
    Route::get('/infografis-peserta/{id}/edit', [OperationController::class, 'edit']);
    Route::put('/infografis-peserta/{id}', [OperationController::class, 'update']);

    //inventaris Alat
    Route::get('/operation/tool-inventory', [OperationController::class, 'tool_inventory'])->name('tool-inventory');
    Route::get('/operation/tool-inventory/usages', [OperationController::class, 'tool_usage'])->name('tool-usage');

    Route::post('/store-asset', [InventoryToolController::class, 'store'])->name('asset.store');
    Route::get('/inventory-tools/{id}/edit', [InventoryToolController::class, 'edit']);
    Route::put('/inventory-tools/{id}', [InventoryToolController::class, 'update']);


    //inventaris ruangan
    Route::get('/operation/room-inventory', [OperationController::class, 'room_inventory'])->name('room-inventory');

    //Penlat Requirement
    Route::get('/penlat/tool-requirement-penlat', [PenlatController::class, 'tool_requirement_penlat'])->name('tool-requirement-penlat');
    Route::post('/store-penlat-requirement', [PenlatController::class, 'requirement_store'])->name('requirement.store');

    //Operation
    Route::get('/operation/utility', [OperationController::class, 'utility'])->name('utility');
    Route::get('/operation/utility/preview-item/{id}', [OperationController::class, 'preview_utility'])->name('preview-utility');
    Route::post('/store-penlat-utility', [OperationController::class, 'utility_store'])->name('utility.store');


    //Marketing
    Route::get('/marketing', [MarketingController::class, 'index'])->name('marketing');
    Route::get('/marketing-campaign', [MarketingController::class, 'campaign'])->name('marketing-campaign');
    Route::get('/marketing-campaign/preview/{id}', [CampaignController::class, 'preview_campaign'])->name('preview-campaign');
    Route::post('/store-campaign', [CampaignController::class, 'store'])->name('campaign.store');
    Route::get('/marketing-campaign/delete-item/{id}', [CampaignController::class, 'delete_campaign'])->name('delete-campaign');
    //Company Agreement
    Route::get('/marketing/company-agreement', [MarketingController::class, 'company_agreement'])->name('company-agreement');
    Route::post('/store-agreement', [AgreementController::class, 'store'])->name('agreement.store');

    //finances
    Route::get('/finances', [FinanceController::class, 'dashboard'])->name('finance');
    Route::get('/finances/vendor-payment', [FinanceController::class, 'vendor_payment'])->name('vendor-payment');
    Route::get('/finances/costs', [FinanceController::class, 'costs'])->name('cost');


    //Plan & Dev
    Route::get('/planning-development', [PDController::class, 'index'])->name('plan-dev');
    Route::get('/planning-development/feedback-report', [PDController::class, 'feedback_report'])->name('feedback-report');
    Route::get('/planning-development/feedback-report-import', [PDController::class, 'feedback_report_import'])->name('feedback-report-import-page');
    Route::get('/planning-development/regulation', [PDController::class, 'regulation'])->name('regulation');
    Route::post('/store-regulation', [PDController::class, 'regulation_store'])->name('regulation.store');
    Route::get('/planning-development/certificate', [PDController::class, 'certificate'])->name('certificate');
    Route::get('/planning-development/instructor', [PDController::class, 'instructor'])->name('instructor');
    Route::get('/planning-development/training-reference', [PDController::class, 'training_reference'])->name('training-reference');


    Route::get('/planning-development/instructor/upload-certificate', [PDController::class, 'upload_certificate'])->name('upload-certificate');



    //my profile
    Route::get('/profile', [MyProfileController::class, 'index'])->name('profile.view');
    Route::post('/profile/reset-password', [MyProfileController::class, 'reset_password'])->name('profile.reset.password');
    Route::post('/profile/change-profile-picture', [MyProfileController::class, 'change_picture'])->name('change.profile.picture');

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
