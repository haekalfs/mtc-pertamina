<?php

use App\Http\Controllers\AgreementController;
use App\Http\Controllers\AkhlakController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentPositionController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\InstagramController;
use App\Http\Controllers\InstructorController;
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
use Illuminate\Support\Facades\Crypt;
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

Route::middleware(['suspicious'])->group(function () {

});

Route::middleware('auth')->group(function () {
    Route::get('/api/chart-data/{year}', [OperationController::class, 'getChartData']);
    Route::get('/api/chart-data-profits/{year}', [FinanceController::class, 'getChartDataProfits']);
    //External User Registration
    Route::get('/user-registration', [RegistrationController::class, 'index'])->name('ext.register.user');
    //KPI
    Route::get('/key-performance-indicators/index/{quarterSelected?}/{yearSelected?}', [KpiController::class, 'index'])->name('kpi');
    Route::get('/encrypt-params', function () {
        $quarter = request('quarter');
        $year = request('year');

        $encryptedQuarter = Crypt::encryptString($quarter);
        $encryptedYear = Crypt::encryptString($year);

        return redirect()->to('/key-performance-indicators/index/' . $encryptedQuarter . '/' . $encryptedYear);
    });
    Route::post('/kpi-store', [KpiController::class, 'store'])->name('kpis.store');
    Route::post('/pencapaian-kpi/store/{kpi}', [KpiController::class, 'store_pencapaian'])->name('pencapaian.kpi.store');
    Route::get('/key-performance-indicators/achievements/{id}/{quarter}/{year}', [KpiController::class, 'pencapaian'])->name('pencapaian-kpi');
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
    Route::get('/operation-dashboard/{yearSelected?}', [OperationController::class, 'index'])->name('operation');

    //penlat
    Route::get('/penlat/list-pelatihan', [PenlatController::class, 'index'])->name('penlat');
    Route::get('/penlat/list-pelatihan/import-data', [PenlatController::class, 'penlat_import'])->name('penlat-import');
    Route::post('/import-list-penlat', [ImportController::class, 'import_penlat'])->name('penlat.import');
    Route::post('/store-penlat', [PenlatController::class, 'store'])->name('penlat.store');
    Route::get('/penlat/{id}/edit', [PenlatController::class, 'edit'])->name('penlat.edit');
    Route::put('/penlat-update/{id}', [PenlatController::class, 'update'])->name('penlat.update');
    Route::delete('/penlat-delete/{id}', [PenlatController::class, 'delete'])->name('delete.penlat');


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
    Route::delete('/asset-delete/{id}', [InventoryToolController::class, 'delete_asset'])->name('delete.asset');


    //inventaris ruangan
    Route::get('/operation/room-inventory', [OperationController::class, 'room_inventory'])->name('room-inventory');
    Route::post('/store-room-inventory', [OperationController::class, 'room_inventory_store'])->name('room.store');
    Route::post('/insert-item-to-room-inventory/{roomId}', [OperationController::class, 'room_inventory_insert_item'])->name('room.insert.item');
    Route::delete('/room-delete/{id}', [OperationController::class, 'delete_room'])->name('delete.room');
    Route::get('/operation/room-inventory/preview-item/{id}', [OperationController::class, 'preview_room'])->name('preview-room');
    Route::get('/operation/room-inventory/preview-room/{id}', [OperationController::class, 'preview_room_user'])->name('preview-room-user');
    Route::get('/room-item-delete/{id}', [OperationController::class, 'delete_item_room'])->name('delete.item.room');
    Route::post('/room-data-update/{roomId}', [OperationController::class, 'update_room_data'])->name('room.data.update');
    Route::put('/room-item-update/{id}', [OperationController::class, 'update_room_item'])->name('room.item.update');

    //Penlat Requirement
    Route::get('/penlat/tool-requirement-penlat', [PenlatController::class, 'tool_requirement_penlat'])->name('tool-requirement-penlat');
    Route::post('/store-penlat-requirement', [PenlatController::class, 'requirement_store'])->name('requirement.store');
    Route::get('/penlat-requirement/{id}/edit', [PenlatController::class, 'edit_requirement'])->name('requirement.data');
    Route::post('/penlat-requirement-update', [PenlatController::class, 'update_requirement'])->name('requirement.update');
    Route::delete('/penlat-requirement-delete/{id}', [PenlatController::class, 'delete_requirement'])->name('delete.requirement');
    Route::get('/penlat-item-requirement-delete/{id}', [PenlatController::class, 'delete_item_requirement'])->name('delete.item.requirement');

    //batch penlat
    Route::get('/penlat/list-batch', [PenlatController::class, 'batch'])->name('batch-penlat');
    Route::get('/penlat/list-batch/preview-batch/{id}', [PenlatController::class, 'preview_batch'])->name('preview-batch');
    Route::post('/store-batch-penlat', [PenlatController::class, 'batch_store'])->name('batch.store');
    Route::get('/penlat-batch/{id}/edit', [PenlatController::class, 'fetch_batch'])->name('batch.data');
    Route::put('/penlat-batch-update/{id}', [PenlatController::class, 'update_batch'])->name('batch.update');
    Route::delete('/penlat-batch-delete/{id}', [PenlatController::class, 'delete_batch'])->name('delete.batch');

    //Operation
    Route::get('/operation/utility', [OperationController::class, 'utility'])->name('utility');
    Route::get('/operation/utility/preview-item/{id}', [OperationController::class, 'preview_utility'])->name('preview-utility');
    Route::post('/store-penlat-utility', [OperationController::class, 'utility_store'])->name('utility.store');
    Route::put('/penlat-utility-update/{id}', [OperationController::class, 'update_utility_usage'])->name('utility.update');
    Route::delete('/penlat-utility-usage-delete/{id}', [OperationController::class, 'delete_batch_usage'])->name('delete.usage');


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
    Route::get('/financial-dashboard/{yearSelected?}', [FinanceController::class, 'dashboard'])->name('finance');
    Route::get('/finances/vendor-payment', [FinanceController::class, 'vendor_payment'])->name('vendor-payment');
    Route::get('/finances/costs', [FinanceController::class, 'costs'])->name('cost');
    Route::get('/finances/costs/import-profits-loss', [FinanceController::class, 'profits_import'])->name('costs.import');
    Route::post('/import-profits-loss', [ImportController::class, 'import_profits'])->name('profits.import');
    Route::get('/finances/costs/preview-item/{id}', [FinanceController::class, 'preview_costs'])->name('preview-costs');


    //Plan & Dev
    Route::get('/planning-development', [PDController::class, 'index'])->name('plan-dev');
    Route::get('/planning-development/feedback-report', [PDController::class, 'feedback_report'])->name('feedback-report');
    Route::get('/planning-development/feedback-report-import', [PDController::class, 'feedback_report_import'])->name('feedback-report-import-page');
    Route::get('/planning-development/regulation', [PDController::class, 'regulation'])->name('regulation');
    Route::post('/store-regulation', [PDController::class, 'regulation_store'])->name('regulation.store');


    Route::get('/planning-development/certification', [PDController::class, 'main_certificate'])->name('certificate-main');
    Route::get('/planning-development/certification/participants-certificate', [PDController::class, 'certificate'])->name('certificate');
    Route::get('/planning-development/certification/instructors-certificate', [PDController::class, 'certificate_instructor'])->name('certificate-instructor');

    Route::post('/store-certificates', [PDController::class, 'certificate_store'])->name('certificate.store');
    Route::post('/store-certificate-catalog', [PDController::class, 'certificate_catalog_store'])->name('certificate-catalog.store');
    Route::get('/planning-development/certificate/preview-item/{id}', [PDController::class, 'preview_certificate'])->name('preview-certificate');
    Route::get('/planning-development/certificate/catalog-item/{id}', [PDController::class, 'preview_certificate_catalog'])->name('preview-certificate-catalog');

    Route::get('/planning-development/instructor', [PDController::class, 'instructor'])->name('instructor');
    Route::get('/planning-development/instructor-preview/{id}/{penlatId}', [PDController::class, 'preview_instructor'])->name('preview-instructor');
    Route::get('/planning-development/instructor-registration', [PDController::class, 'register_instructor'])->name('register-instructor');
    Route::get('/planning-development/instructor-update-data/{instructorId}', [InstructorController::class, 'edit_instructor'])->name('edit-instructor');
    Route::put('/planning-development/instructor-update/{instructorId}', [InstructorController::class, 'update'])->name('instructor.update');
    Route::post('/planning-development/instructor-store', [InstructorController::class, 'store'])->name('instructor.store');

    Route::get('/planning-development/training-reference', [PDController::class, 'training_reference'])->name('training-reference');
    Route::post('/store-penlat-references', [PDController::class, 'references_store'])->name('references.store');


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
