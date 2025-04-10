<?php

use App\Http\Controllers\AgreementController;
use App\Http\Controllers\AkhlakController;
use App\Http\Controllers\AmendmentController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\BarcodeController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentPositionController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\InstructorController;
use App\Http\Controllers\InventoryApprovalController;
use App\Http\Controllers\InventoryToolController;
use App\Http\Controllers\KPIController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ManageAccessController;
use App\Http\Controllers\MarketingController;
use App\Http\Controllers\MonitoringApprovalController;
use App\Http\Controllers\MorningBriefingController;
use App\Http\Controllers\MyProfileController;
use App\Http\Controllers\OperationController;
use App\Http\Controllers\PDController;
use App\Http\Controllers\PenlatController;
use App\Http\Controllers\RefractorController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\UserController;
use App\Models\Infografis_peserta;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Laravel\Fortify\Actions\DisableTwoFactorAuthentication;
use Laravel\Fortify\Actions\EnableTwoFactorAuthentication;
use Laravel\Fortify\Actions\GenerateNewRecoveryCodes;
use Laravel\Fortify\Http\Controllers\TwoFactorAuthenticationController;
use Laravel\Fortify\Http\Controllers\TwoFactorQrCodeController;
use Laravel\Fortify\Http\Controllers\TwoFactorSecretKeyController;
use Laravel\Fortify\Http\Controllers\RecoveryCodeController;
use Yajra\DataTables\Facades\DataTables;
use Laravel\Fortify\Http\Controllers\TwoFactorAuthenticatedSessionController;

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

Route::middleware('checkForErrors', 'suspiciousTexts', 'suspicious', 'throttle:60,1')->group(function () {
    Route::get('/certificate-validation/qr-code/preview-certificate/{id}', [PDController::class, 'validate_certificate'])->name('validate-certificate');
});


// Throttle middleware for 10 requests per minute
Route::get('/', function () {
    return redirect('/dashboard');
})->middleware(['auth', 'suspicious', 'suspiciousTexts', 'verified', 'throttle:30,1']);

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'suspicious', 'suspiciousTexts', 'restrictRequestbyRole', 'verified']) // 60 requests per minute
    ->name('dashboard');

// Protect the Two-Factor Verification Route
Route::post('/two-factor-verify', [AuthenticatedSessionController::class, 'verifyTwoFactor'])
    ->name('two-factor.verify')
    ->middleware('throttle:5,1', 'suspiciousTexts', 'checkForErrors', 'suspicious'); // Allow only 5 attempts per minute

// Protect Two-Factor Challenge Routes
Route::middleware(['suspiciousTexts', 'checkForErrors', 'suspicious', 'guest'])->group(function () {
    Route::get('/two-factor-challenge', [TwoFactorAuthenticatedSessionController::class, 'create'])
        ->name('two-factor.login')
        ->middleware('throttle:10,1'); // Allow 10 requests per minute

    Route::post('/two-factor-challenge', [TwoFactorAuthenticatedSessionController::class, 'store'])
        ->middleware('throttle:5,1'); // Allow only 5 attempts per minute
});

Route::middleware('checkForErrors', 'suspicious', 'auth')->group(function () {
    Route::middleware('suspiciousTexts')->group(function () {
        // Enable Two-Factor Authentication
        Route::post('/user/two-factor-authentication', [TwoFactorAuthenticationController::class, 'store']);
        Route::delete('/user/two-factor-authentication', [TwoFactorAuthenticationController::class, 'destroy']);
        // Get Recovery Codes
        Route::get('/user/two-factor-recovery-codes', [RecoveryCodeController::class, 'index']);
        Route::post('/user/two-factor-recovery-codes', [RecoveryCodeController::class, 'store']);
        // Generate QR Code for Authenticator App
        Route::get('/user/two-factor-qr-code', [TwoFactorQrCodeController::class, 'show']);
        // Generate Secret Key
        Route::get('/user/two-factor-secret-key', [TwoFactorSecretKeyController::class, 'show']);

        Route::get('/penlat/list', [PenlatController::class, 'getPenlatList'])->name('penlat.list');
        Route::get('/fetch-penlat-batches', [PenlatController::class, 'fetchBatches'])->name('batches.fetch');
        Route::get('/fetch-certificate-batches', [PenlatController::class, 'fetchBatchesCertificates'])->name('batches.fetch.certificate');

        //On Dev Notification
        Route::get('/closed-menu', function () {
            Session::flash('warning', 'The menu you try to access is locked 🔒, thank you for your patience.');
            return redirect()->back();
        })->name('on.development');

        //Toast
        Route::post('/clear-toast-session', function () {
            Session::forget('loading-import');
            return response()->json(['status' => 'success']);
        })->name('clear.toast.session');

        //Charts set up in RouteServiceProvider.php
        Route::middleware(['throttle:chart-data'])->group(function () {
            Route::get('/api/chart-data/{year}', [OperationController::class, 'getChartData']);
            Route::get('/api/chart-data-profits/{year}', [FinanceController::class, 'getChartDataProfits']);
            Route::get('/api/chart-data-trend-revenue/{year}', [FinanceController::class, 'getTrendChartData']);
            Route::get('/api/pie-chart-data-profits/{year}', [FinanceController::class, 'getPieChartData']);
            Route::get('/api/comparison-chart-data-profits/{year}/{secondYear}', [FinanceController::class, 'getComparisonChartData']);
            Route::get('/api/summary-data-profits/{year}', [FinanceController::class, 'getSummaryProfits']);
            Route::get('/feedback-chart-data/{year}', [PDController::class, 'getFeedbackChartData']);
            Route::get('/feedback-MTC-chart-data/{yearSelected}', [PDController::class, 'getFeedbackMTCChartData']);
            Route::get('/check-user-id/{userId}', [UserController::class, 'checkUserId']);
            Route::get('/api/get-penlat-batch-events', [DashboardController::class, 'getEvents'])->name('penlat.batch.events');
            Route::get('/api/get-infografis-peserta', [DashboardController::class, 'getInfografisPeserta']);

            //will be used
            Route::get('/download-file/{filepath}', [DownloadController::class, 'downloadFile'])->name('download.file');
            Route::post('/api/fetchAmountData', [DashboardController::class, 'fetchAmountData'])->name('fetchAmountData');
            Route::post('/api/chart-bar-data', [DashboardController::class, 'fetchChartsData'])->name('fetchChartData');

            Route::post('/api/chart-trend-revenue-data', [DashboardController::class, 'fetchTrendRevenueData']);
            Route::post('/api/chart-drilldown-revenue-data', [DashboardController::class, 'fetchDrilldownRevenueData']);
            Route::post('/api/fetch-participants-by-revenue', [DashboardController::class, 'fetchParticipantsByRevenue']);

            Route::post('/api/chart-location-data', [DashboardController::class, 'fetchLocationChartData']);
            Route::post('/api/chart-location-drilldown', [DashboardController::class, 'fetchDrilldownChartData']);
            Route::post('/api/fetch-participants-by-location', [DashboardController::class, 'fetchParticipantsByLocation']);

            Route::post('/api/chart-training-type-data', [DashboardController::class, 'fetchTrainingTypeChartData']);
            Route::post('/api/chart-training-type-drilldown', [DashboardController::class, 'fetchTrainingTypeDrilldownData']);
            Route::post('/api/fetch-participants-by-training-type', [DashboardController::class, 'fetchParticipantsByTrainingType']);

            Route::post('/api/chart-overall-data', [DashboardController::class, 'fetchOverallData']);
            Route::post('/api/fetch-participants-by-overall', [DashboardController::class, 'fetchParticipantsByOverall']);

            Route::post('/api/chart-issued-certificate-data', [DashboardController::class, 'getIssuedCertificateData']);
            Route::post('/api/get-participants', [DashboardController::class, 'getChartDetail']);
            Route::post('/api/get-participants-stcw-non', [DashboardController::class, 'getParticipants']);

            Route::get('/api/asset-charts/{id}', [InventoryToolController::class, 'getAssetChartData'])->name('asset.chart.data');
            Route::get('/approval/inventory-tool/data', [InventoryApprovalController::class, 'getData'])->name('approval.inventory-tool.data');
            Route::post('/approval/inventory-tool/approve', [InventoryApprovalController::class, 'approveChanges'])->name('approval.inventory-tool.approve');
            Route::post('/approval/inventory-tool/reject', [InventoryApprovalController::class, 'rejectChanges'])->name('approval.inventory-tool.approve.reject');

            Route::post('/roles/update-superadmin/{id}', [RolesController::class, 'updateSuperAdmin'])->name('roles.updateSuperAdmin');
        });

        //Encrypt
        Route::get('/encrypt-params', function () {
            $quarter = request('quarter');
            $year = request('year');

            $encryptedQuarter = Crypt::encryptString($quarter);
            $encryptedYear = Crypt::encryptString($year);

            return redirect()->to('/key-performance-indicators/index/' . $encryptedQuarter . '/' . $encryptedYear);
        });

        Route::middleware(['restrictRequestbyRole'])->group(function () {
            Route::middleware(['checkUserAccess:100'])->group(function () {
                //participant-infographics
                Route::get('/operation/participant-infographics', [OperationController::class, 'participant_infographics'])->name('participant-infographics');
                Route::get('/operation/participant-infographics/import-page', [OperationController::class, 'participant_infographics_import_page'])->name('participant-infographics-import-page');
                Route::post('/import-infografis-peserta', [ImportController::class, 'import'])->name('infografis_peserta.import');
                Route::post('/infografis-peserta-store', [OperationController::class, 'infografis_store'])->name('infografis.store');
                Route::get('/infografis-peserta/{id}/edit', [OperationController::class, 'edit']);
                Route::put('/infografis-peserta/{id}', [OperationController::class, 'update']);
                Route::delete('/infografis-peserta-delete-data/{id}', [OperationController::class, 'delete_data_peserta']);

                //Error Log
                Route::get('/operation/participant-infographics/error-log', [OperationController::class, 'error_log'])->name('infographics.error.log');

                //penlat
                Route::get('/penlat/list-pelatihan', [PenlatController::class, 'index'])->name('penlat');
                Route::get('/penlat/pelatihan-preview/{penlatId}', [PenlatController::class, 'preview_penlat'])->name('preview-penlat');
                Route::get('/penlat/list-pelatihan/import-data', [PenlatController::class, 'penlat_import'])->name('penlat-import');
                Route::post('/import-list-penlat', [ImportController::class, 'import_penlat'])->name('penlat.import');
                Route::post('/store-penlat', [PenlatController::class, 'store'])->name('penlat.store');
                Route::get('/penlat/{id}/edit', [PenlatController::class, 'edit'])->name('penlat.edit');
                Route::put('/penlat-update/{id}', [PenlatController::class, 'update'])->name('penlat.update');
                Route::delete('/penlat-delete/{id}', [PenlatController::class, 'delete'])->name('delete.penlat');

                //batch penlat
                Route::get('/penlat/list-batch', [PenlatController::class, 'batch'])->name('batch-penlat');
                Route::get('/penlat/list-batch/preview-batch/{id}', [PenlatController::class, 'preview_batch'])->name('preview-batch');
                Route::post('/store-batch-penlat', [PenlatController::class, 'batch_store'])->name('batch.store');
                Route::get('/penlat-batch/{id}/edit', [PenlatController::class, 'fetch_batch'])->name('batch.data');
                Route::put('/penlat-batch-update/{id}', [PenlatController::class, 'update_batch'])->name('batch.update');
                Route::delete('/penlat-batch-delete/{id}', [PenlatController::class, 'delete_batch'])->name('delete.batch');
                Route::post('/penlat-batch/refresh', [PenlatController::class, 'refresh_batch_data'])->name('refresh.batch');
                //Certificate Number
                Route::get('/master-data/certificates-numbers', [CertificateController::class, 'index'])->name('certificate-number');
            });

            Route::middleware(['checkUserAccess:101'])->group(function () {
                //Operation
                Route::get('/operation-dashboard/{yearSelected?}', [OperationController::class, 'index'])->name('operation');

                //inventaris Alat
                Route::get('/tool-inventory/index', [InventoryToolController::class, 'main'])->name('mainpage-inventory');
                Route::get('/tool-inventory/audit-log', [InventoryToolController::class, 'audit_log'])->name('audit-inventory');
                Route::get('/tool-inventory', [InventoryToolController::class, 'tool_inventory'])->name('tool-inventory');
                Route::get('/tool-inventory/preview/{id}', [InventoryToolController::class, 'preview_asset'])->name('preview-asset');
                //QR CODE PREVIEWER
                Route::get('/tool-inventory-validation/qr-code/preview-asset/{id}', [InventoryToolController::class, 'validate_asset'])->name('validate-asset');

                Route::post('/store-asset', [InventoryToolController::class, 'store'])->name('asset.store');
                Route::get('/inventory-tools/{id}/edit', [InventoryToolController::class, 'edit']);
                Route::get('/inventory-tools-view-info/{id}', [InventoryToolController::class, 'fetch_info']);
                Route::put('/inventory-tools-update/{id}', [InventoryToolController::class, 'update'])->name('update.asset');
                Route::put('/inventory-tools-update-partially/{id}', [InventoryToolController::class, 'update_partially'])->name('update.asset.partially');
                Route::delete('/asset-delete/{id}', [InventoryToolController::class, 'delete_asset'])->name('delete.asset');
                Route::patch('/inventory-tools-mark-as-used/{id}', [InventoryToolController::class, 'markAsUsed'])->name('inventory-tools.mark-as-used');
                Route::patch('/inventory-tools-mark-as-unused/{id}', [InventoryToolController::class, 'markAsUnused'])->name('inventory-tools.mark-as-unused');
                Route::get('/tool-inventory-generate-qr/{id}', [InventoryToolController::class, 'generateQrCode'])->name('generate-qr');
                Route::post('/update-asset-condition', [InventoryToolController::class, 'updateAssetCondition'])->name('update-asset-condition');
                Route::delete('/inventory-tools/delete/{id}', [InventoryToolController::class, 'destroy_asset_per_item'])->name('inventory-tools.delete');

                //approval
                Route::get('/approval', [ApprovalController::class, 'index'])->name('index-approval');
                Route::get('/approval/inventory-tools', [InventoryApprovalController::class, 'index'])->name('inventory-tools-approval');


                Route::post('/tool-maintenance-update', [InventoryToolController::class, 'maintenanceUpdate'])->name('maintenance.update');
                Route::post('/tool-set-as-used', [InventoryToolController::class, 'setAsUsed'])->name('set.used');
                Route::post('/tool-set-as-unused', [InventoryToolController::class, 'setAsUnused'])->name('set.unused');
                Route::post('/tool-change-state', [InventoryToolController::class, 'changeConditions'])->name('updateConditions');


                //inventaris ruangan
                Route::get('/operation/room-inventory', [OperationController::class, 'room_inventory'])->name('room-inventory');
                Route::post('/store-room-inventory', [OperationController::class, 'room_inventory_store'])->name('room.store');
                Route::post('/insert-item-to-room-inventory/{roomId}', [OperationController::class, 'room_inventory_insert_item'])->name('room.insert.item');
                Route::delete('/room-delete/{id}', [OperationController::class, 'delete_room'])->name('delete.room');
                Route::get('/operation/room-inventory/preview-item/{id}', [OperationController::class, 'preview_room'])->name('preview-room');
                Route::get('/operation/room-inventory/preview-room/{id}', [OperationController::class, 'preview_room_user'])->name('preview-room-user');
                Route::delete('/room-item-delete/{id}', [OperationController::class, 'delete_item_room'])->name('delete.item.room');
                Route::post('/room-data-update/{roomId}', [OperationController::class, 'update_room_data'])->name('room.data.update');
                Route::put('/room-item-update/{id}', [OperationController::class, 'update_room_item'])->name('room.item.update');

                //Utility
                Route::get('/operation/utility', [OperationController::class, 'utility'])->name('utility');
                Route::get('/operation/utility/preview-item/{id}', [OperationController::class, 'preview_utility'])->name('preview-utility');
                Route::post('/store-penlat-utility', [OperationController::class, 'utility_store'])->name('utility.store');
                Route::put('/penlat-utility-update/{id}', [OperationController::class, 'update_utility_usage'])->name('utility.update');
                Route::post('/insert-new-item-penlat-utility/{id}', [OperationController::class, 'utility_insert_new_item'])->name('utility.insert');
                Route::delete('/penlat-utility-item-usage-delete/{id}', [OperationController::class, 'delete_item_usage'])->name('delete.item.usage');
                Route::delete('/penlat-utility-usage-delete/{id}', [OperationController::class, 'delete_batch_usage'])->name('delete.usage');

                //Penlat Requirement
                Route::get('/penlat/tool-requirement-penlat', [PenlatController::class, 'tool_requirement_penlat'])->name('tool-requirement-penlat');
                Route::post('/store-penlat-requirement', [PenlatController::class, 'requirement_store'])->name('requirement.store');
                Route::post('/insert-item-to-penlat-requirement/{penlatId}', [PenlatController::class, 'requirement_insert_item'])->name('requirement.insert.item');
                Route::get('/penlat/tool-requirement-penlat/preview-item/{id}', [PenlatController::class, 'preview_requirement'])->name('preview-requirement');
                Route::put('/penlat-requirement-update/{id}', [PenlatController::class, 'update_requirement'])->name('requirement.update');
                Route::delete('/penlat-requirement-delete/{id}', [PenlatController::class, 'delete_requirement'])->name('delete.requirement');
                Route::delete('/penlat-item-requirement-delete/{id}', [PenlatController::class, 'delete_item_requirement'])->name('delete.item.requirement');
            });

            // P&D
            Route::middleware(['checkUserAccess:103'])->group(function () {
                //Plan & Dev
                Route::get('/planning-development-dashboard/{yearSelected?}', [PDController::class, 'index'])->name('plan-dev');

                Route::get('/planning-development/feedback-report-mainpage', [PDController::class, 'feedback_report_main'])->name('feedback-report-main');
                Route::get('/planning-development/feedback-mtc', [FeedbackController::class, 'feedback_mtc'])->name('feedback-mtc');
                Route::get('/planning-development/feedback-report', [PDController::class, 'feedback_report'])->name('feedback-report');

                Route::get('/planning-development/feedback-report-import', [PDController::class, 'feedback_report_import'])->name('feedback-report-import-page');
                Route::post('/import-feedback-report', [ImportController::class, 'import_feedback'])->name('feedback.import');

                Route::get('/feedback-report/{id}/edit', [FeedbackController::class, 'edit_feedback'])->name('fetch.feedback.data');
                Route::put('/feedback-report/{id}', [FeedbackController::class, 'update_feedback'])->name('feedback.update');
                Route::delete('/feedback-instructor-delete-data/{id}', [FeedbackController::class, 'delete_feedback_instructor']);

                Route::get('/planning-development/feedback-mtc-import', [FeedbackController::class, 'feedback_mtc_import'])->name('feedback-mtc-import-page');
                Route::post('/import-feedback-mtc-report', [ImportController::class, 'import_feedback_mtc'])->name('feedback.mtc.import');
                Route::get('/feedback-mtc-edit/{id}', [FeedbackController::class, 'edit_feeedback_mtc'])->name('feedback-mtc.edit');
                Route::put('/feedback-mtc-update/{id}', [FeedbackController::class, 'update_feeedback_mtc'])->name('feedback-mtc.update');
                Route::delete('/feedback-mtc-delete-data/{id}', [FeedbackController::class, 'delete_feedback_mtc']);

                Route::get('/planning-development/regulation', [PDController::class, 'regulation'])->name('regulation');
                Route::post('/store-regulation', [PDController::class, 'regulation_store'])->name('regulation.store');
                Route::get('/planning-development/regulation-view/{itemId}', [PDController::class, 'preview_regulation'])->name('preview-regulation');
                Route::delete('/delete-regulation/{id}', [PDController::class, 'delete_regulation'])->name('regulation.destroy');
                Route::put('/update-regulation', [PDController::class, 'update_regulation'])->name('regulation.update');

                Route::get('/planning-development/monitoring-approval', [MonitoringApprovalController::class, 'index'])->name('monitoring-approval');
                Route::post('/store-monitoring-approval', [MonitoringApprovalController::class, 'store'])->name('monitoring-approval.store');
                Route::get('/planning-development/monitoring-approval-view/{itemId}', [MonitoringApprovalController::class, 'preview'])->name('preview-monitoring-approval');
                Route::delete('/delete-monitoring-approval/{id}', [MonitoringApprovalController::class, 'delete'])->name('monitoring-approval.destroy');
                Route::put('/update-monitoring-approval', [MonitoringApprovalController::class, 'update'])->name('monitoring-approval.update');


                Route::get('/planning-development/certification', [PDController::class, 'main_certificate'])->name('certificate-main');
                Route::get('/planning-development/certification/participants-certificate', [PDController::class, 'certificate'])->name('certificate');
                Route::get('/planning-development/certification/instructors-certificate', [PDController::class, 'certificate_instructor'])->name('certificate-instructor');

                Route::post('/store-certificates', [PDController::class, 'certificate_store'])->name('certificate.store');
                Route::post('/mark-certificate-received', [PDController::class, 'markCertificateAsReceived'])->name('mark.received');
                Route::post('/mark-certificate-expire', [PDController::class, 'markCertificateAsExpire'])->name('mark.expired');
                Route::post('/certificate/{id}/update', [PDController::class, 'updateCertificate'])->name('certificate.list.update');
                Route::get('/certificates/{id}', [PDController::class, 'getCertificates'])->name('certificates.get');
                Route::post('/certificates/export/selected-items', [PDController::class, 'export_selected'])->name('certificate.export.selected');
                Route::post('/certificates/set-as-issued/selected-items', [PDController::class, 'markAsIssued'])->name('set-as-issued');
                Route::post('/refresh-participants-certificate', [PDController::class, 'refreshParticipants'])->name('refresh.participants');
                Route::post('/export-certificate-data', [CertificateController::class, 'export'])->name('export.certificate.data');
                Route::post('/export-batches-data', [PenlatController::class, 'export'])->name('export.batches.data');
                Route::get('/certificate-generate-qr/{id}', [PDController::class, 'generateQrCode'])->name('generate-qr-certificate');
                Route::get('/get-next-certificate-number/{id}', [PDController::class, 'getNumberCertificate'])->name('getCertificateNumber');
                Route::get('/penlat/amendments', [PenlatController::class, 'getAmendments'])->name('penlat.getAmendments');


                Route::get('/regulators/fetch', [PDController::class, 'fetchRegulators'])->name('regulators.fetch');
                Route::post('/regulators/store', [PDController::class, 'storeRegulator'])->name('regulators.store');
                // Route::get('/generateExcelWithQrCode/{id}', [PDController::class, 'generateExcelWithQrCode']);

                Route::post('/planning-development/certificate-update/{certId}', [PDController::class, 'certificate_update'])->name('certificate.update');
                Route::post('/certificate/delete', [PDController::class, 'delete_certificate'])->name('certificate.delete');
                Route::post('/store-certificate-catalog', [PDController::class, 'certificate_catalog_store'])->name('certificate-catalog.store');
                Route::get('/planning-development/certificate/preview-item/{id}', [PDController::class, 'preview_certificate'])->name('preview-certificate');
                Route::post('/participant/save', [PDController::class, 'save_receivable'])->name('receivable.participant.save');
                Route::post('/participant/delete', [PDController::class, 'delete_receivable'])->name('receivable.participant.delete');
                Route::post('/receivables/participants/saveAll', [PDController::class, 'saveAllReceivables'])->name('receivable.participants.saveAll');
                Route::get('/planning-development/certificate/catalog-item/{id}', [PDController::class, 'preview_certificate_catalog'])->name('preview-certificate-catalog');
                Route::delete('/instructor-certificates-catalog/delete/{id}', [PDController::class, 'deleteCertificate'])->name('certificates_catalog.delete');

                Route::get('/planning-development/instructor', [PDController::class, 'instructor'])->name('instructor');
                Route::get('/planning-development/instructor-preview/{id}/{penlatId}', [PDController::class, 'preview_instructor'])->name('preview-instructor');
                Route::get('/planning-development/instructor-registration', [PDController::class, 'register_instructor'])->name('register-instructor');
                Route::get('/planning-development/instructor-update-data/{instructorId}', [InstructorController::class, 'edit_instructor'])->name('edit-instructor');
                Route::put('/planning-development/instructor-update/{instructorId}', [InstructorController::class, 'update'])->name('instructor.update');
                Route::post('/planning-development/instructor-update-hours/{id}', [InstructorController::class, 'update_hours'])->name('instructor.update.hours');
                Route::post('/planning-development/instructor-store', [InstructorController::class, 'store'])->name('instructor.store');
                Route::delete('/planning-development/instructor-delete/{id}', [InstructorController::class, 'deleteInstructor'])->name('instructor.delete');
                Route::get('/get-instructors', [InstructorController::class, 'getInstructors'])->name('get.instructors');


                Route::get('/planning-development/training-reference', [PDController::class, 'training_reference'])->name('training-reference');
                Route::post('/store-penlat-references', [PDController::class, 'references_store'])->name('references.store');
                Route::delete('/delete-training-reference/{penlat_id}', [PDController::class, 'deleteTrainingReference'])->name('training_reference.delete');
                Route::post('/insert-new-item-penlat-references/{penlatId}', [PDController::class, 'references_insert'])->name('references.new.item');
                Route::get('/planning-development/preview/training-reference/{id}', [PDController::class, 'preview_reference'])->name('preview-training-reference');
                Route::delete('/delete-item-training-reference/{id}', [PDController::class, 'destroy_reference'])->name('training-reference.destroy');
                Route::get('/fetch-penlat-references/{penlatId}', [PDController::class, 'fetch_reference_data'])->name('references.data');
                Route::post('/update-penlat-references', [PDController::class, 'update_references'])->name('references.update');
            });

            // Marketing
            Route::middleware(['checkUserAccess:104'])->group(function () {
                //Marketing
                Route::get('/marketing', [MarketingController::class, 'index'])->name('marketing');
                Route::get('/marketing-campaign', [MarketingController::class, 'campaign'])->name('marketing-campaign');
                Route::get('/marketing-campaign/preview/{id}', [CampaignController::class, 'preview_campaign'])->name('preview-campaign');
                Route::post('/store-campaign', [CampaignController::class, 'store'])->name('campaign.store');
                Route::get('/fetch-campaign-data/{itemId}', [CampaignController::class, 'show'])->name('campaign.show');
                Route::put('/update-campaign/{itemId}', [CampaignController::class, 'update'])->name('campaign.update');
                Route::delete('/marketing-campaign/delete-item/{id}', [CampaignController::class, 'delete_campaign'])->name('delete-campaign');

                //Company Agreement
                Route::get('/marketing/company-agreement', [MarketingController::class, 'company_agreement'])->name('company-agreement');
                Route::post('/store-agreement', [AgreementController::class, 'store'])->name('agreement.store');
                Route::get('/marketing/company-agreement-preview/{id}', [AgreementController::class, 'preview_agreement'])->name('preview-company');
                Route::get('/fetch-agreement-data/{itemId}', [AgreementController::class, 'show'])->name('agreement.show');
                Route::put('/update-agreement/{itemId}', [AgreementController::class, 'update'])->name('agreement.update');
                Route::delete('/marketing-agreement/delete-item/{id}', [AgreementController::class, 'delete_agreement'])->name('delete-agreement');
            });

            Route::middleware(['checkUserAccess:203'])->group(function () {
                //Master Data
                Route::get('/utilities/list-utilities', [OperationController::class, 'list_utilities'])->name('list-utilities');
                Route::post('/store-data-utilities', [OperationController::class, 'store_new_utility'])->name('store-new-utility');
                Route::delete('/delete-utility-data', [OperationController::class, 'deleteUtility'])->name('delete-utility');
                Route::get('/get-utility/{id}', [OperationController::class, 'getUtility'])->name('get-utility');
                Route::post('/update-utility', [OperationController::class, 'updateUtility'])->name('update-utility');

                Route::get('/master-data/list-location', [LocationController::class, 'index'])->name('list-location');
                Route::post('/store-data-location', [LocationController::class, 'store'])->name('store-new-location');
                Route::delete('/locations/{id}', [LocationController::class, 'destroy'])->name('locations.destroy');
                Route::put('/locations/{id}', [LocationController::class, 'update'])->name('locations.update');
                Route::get('/locations/{id}/edit', [LocationController::class, 'edit'])->name('locations.edit');


                //List Amendment
                Route::get('/master-data/list-amendment', [AmendmentController::class, 'index'])->name('list-amendment');
                Route::post('/store-data-amendment', [AmendmentController::class, 'store'])->name('store-new-amendment');
                Route::delete('/amendments/{id}', [AmendmentController::class, 'destroy'])->name('amendments.destroy');
                Route::put('/amendments/{id}', [AmendmentController::class, 'update'])->name('amendments.update');
                Route::get('/amendments/{id}/edit', [AmendmentController::class, 'edit'])->name('amendments.edit');
            });


            //Finance
            Route::middleware(['checkUserAccess:102'])->group(function () {
                //finances
                Route::get('/financial-dashboard/{yearSelected?}', [FinanceController::class, 'dashboard'])->name('finance');
                Route::get('/finances/vendor-payment', [FinanceController::class, 'vendor_payment'])->name('vendor-payment');
                Route::get('/finances/vendor-payment-import-page', [FinanceController::class, 'vendor_payment_import'])->name('vendor-payment-importer');
                Route::post('/import-vendor-payments', [ImportController::class, 'import_vendor_payment'])->name('vendor_payment.import');

                Route::get('/finances/profits-loss', [FinanceController::class, 'profits'])->name('profits');
                Route::get('/finances/costs/{penlatId}/{month}/{year}', [FinanceController::class, 'costs'])->name('cost');
                Route::get('/finances/profit-loss', [FinanceController::class, 'profit_loss'])->name('profit-loss');
                Route::get('/finances/import-profits-loss-page', [FinanceController::class, 'profits_import'])->name('costs.import');
                Route::post('/import-profits-loss', [ImportController::class, 'import_profits'])->name('profits.import');
                Route::get('/finances/costs/preview-item/{id}', [FinanceController::class, 'preview_costs'])->name('preview-costs');

                //Error Log
                Route::get('/finances/error-log', [FinanceController::class, 'error_log'])->name('finance.error.log');
            });

            //KPI
            Route::middleware(['checkUserAccess:105'])->group(function () {
                //KPI
                Route::get('/key-performance-indicators/index/{quarterSelected?}/{yearSelected?}', [KPIController::class, 'index'])->name('kpi');
                Route::post('/kpi-store', [KPIController::class, 'store'])->name('kpis.store');
                Route::get('/kpi/{id}/edit', [KPIController::class, 'edit'])->name('kpis.edit');
                Route::put('/kpi-update/{id}', [KPIController::class, 'update'])->name('kpis.update');
                Route::post('/pencapaian-kpi/store/{kpi}', [KPIController::class, 'store_pencapaian'])->name('pencapaian.kpi.store');
                Route::get('/pencapaian-kpi/edit/{id}', [KPIController::class, 'edit_pencapaian'])->name('pencapaian.kpi.edit');
                Route::delete('/pencapaian-kpi/delete/{id}', [KPIController::class, 'delete_pencapaian'])->name('pencapaian.kpi.delete');
                Route::post('/pencapaian-kpi/update/{id}', [KPIController::class, 'update_pencapaian'])->name('pencapaian.update');
                Route::get('/key-performance-indicators/achievements/{id}/{quarter}/{year}', [KPIController::class, 'pencapaian'])->name('pencapaian-kpi');
                Route::delete('/delete-kpi/{id}', [KPIController::class, 'destroy'])->name('kpi.destroy');
                Route::delete('/delete-pencapaian-kpi/{id}', [KPIController::class, 'destroy_pencapaian'])->name('pencapaian.kpi.destroy');
                Route::get('/key-performance-indicators/manage-items/{yearSelected?}', [KPIController::class, 'manage'])->name('manage-kpi');
                Route::get('/key-performance-indicators/report', [KPIController::class, 'report'])->name('report-kpi');
                Route::post('/duplicate-kpis/{year}', [KPIController::class, 'duplicateKpis'])->name('kpis.duplicate');
                Route::post('/key-performance-indicators/downloadPdf', [KPIController::class, 'downloadPdf'])->name('kpi.downloadPdf');
                //KPI Unused
                Route::get('/key-performance-indicators/preview/{id}', [KPIController::class, 'preview'])->name('preview-kpi');
            });

            //Pencapaian AKhlak
            Route::middleware(['checkUserAccess:106'])->group(function () {
                Route::get('/akhlak-achievements', [AkhlakController::class, 'index'])->name('akhlak.achievements');
                Route::get('/akhlak/preview-achievements/{coreValue}/{quarter}/{periode}', [AkhlakController::class, 'preview_achievements'])->name('preview.achievements');
                Route::get('/akhlak-report', [AkhlakController::class, 'report'])->name('report-akhlak');
                Route::post('/akhlak-store', [AkhlakController::class, 'store'])->name('akhlak.store');
                Route::get('/akhlak/edit/{id}', [AkhlakController::class, 'edit'])->name('akhlak.edit');
                Route::put('/akhlak/update/{id}', [AkhlakController::class, 'update'])->name('akhlak.update');
                Route::delete('/akhlak-destroy/{id}', [AkhlakController::class, 'destroy'])->name('akhlak.destroy');
                Route::post('/akhlak/downloadPdf', [AkhlakController::class, 'downloadPdf'])->name('akhlak.downloadPdf');
            });

            //my profile
            Route::get('/profile', [MyProfileController::class, 'index'])->name('profile.view');
            Route::post('/profile/reset-password', [MyProfileController::class, 'reset_password'])->name('profile.reset.password');
            Route::post('/profile/change-profile-picture', [MyProfileController::class, 'change_picture'])->name('change.profile.picture');

            // Manage Users
            Route::middleware(['checkUserAccess:201'])->group(function () {
                //Manage Users
                Route::get('/manage-users', [UserController::class, 'manage'])->name('manage.users');
                Route::get('/manage-users/registration', [UserController::class, 'register'])->name('register.users');
                Route::post('/register/user', [UserController::class, 'store'])->name('register.user');
                Route::get('/manage-users/preview-user/{id}', [UserController::class, 'preview'])->name('preview.user');
                Route::post('/manage-users/reset-password/{userId}', [UserController::class, 'reset_user_password'])->name('reset.user.password');
                Route::get('/manage-users/edit-user/{id}', [UserController::class, 'edit'])->name('edit.user');
                Route::put('/manage-users/update-user/{userId}', [UserController::class, 'update'])->name('update.user');
                Route::delete('/manage-users/user-deletion/{id}', [UserController::class, 'delete'])->name('user.delete');

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

                // Enable MFA for another user
                Route::post('/admin/user/{id}/enable-mfa', function ($id, EnableTwoFactorAuthentication $enable) {
                    $user = User::findOrFail($id);
                    $enable($user);
                    app(GenerateNewRecoveryCodes::class)($user); // Generate recovery codes

                    return back()->with('success', 'MFA enabled for the user.');
                })->middleware('auth');

                // Disable MFA for another user
                Route::delete('/admin/user/{id}/disable-mfa', function ($id, DisableTwoFactorAuthentication $disable) {
                    $user = User::findOrFail($id);
                    $disable($user);
                    $user->forceFill(['two_factor_recovery_codes' => null])->save();

                    return back()->with('success', 'MFA disabled for the user.');
                })->middleware('auth');

                // Get QR Code for another user
                Route::get('/admin/user/{id}/two-factor-qr-code', function ($id) {
                    $user = User::findOrFail($id);
                    return response($user->twoFactorQrCodeSvg())->header('Content-Type', 'image/svg+xml');
                })->middleware('auth');
            });

            Route::middleware(['checkUserAccess:202'])->group(function () {
                //manage access
                Route::get('/manage-access', [ManageAccessController::class, 'index'])->name('manage.access');
                Route::post('/assign-role-to-page', [ManageAccessController::class, 'grant_access_to_roles'])->name('assign.roles.to.page');
                Route::get('/reset-access/{id}', [ManageAccessController::class, 'remove_access'])->name('remove.access');
            });

            Route::middleware(['checkUserAccess:204'])->group(function () {
                //Refractore Data
                Route::get('/data-management/refractor', [RefractorController::class, 'index'])->name('refractor');
                Route::post('/refractor-data-deletion', [RefractorController::class, 'data_deletion'])->name('delete.data');
            });
        });
    });

    Route::middleware(['restrictRequestbyRole', 'checkUserAccess:105'])->group(function () {
        Route::post('/key-performance-indicators/downloadPdf', [KPIController::class, 'downloadPdf'])->name('kpi.downloadPdf');
    });
    Route::middleware(['restrictRequestbyRole', 'checkUserAccess:106'])->group(function () {
        Route::post('/akhlak/downloadPdf', [AkhlakController::class, 'downloadPdf'])->name('akhlak.downloadPdf');
    });

    Route::middleware(['restrictRequestbyRole', 'suspiciousTexts'])->group(function () {
        Route::middleware(['checkUserAccess:106'])->group(function () {
            Route::get('/akhlak/morning-briefing', [MorningBriefingController::class, 'index'])->name('morning-briefing');
            Route::get('/akhlak/morning-briefing/preview/{id}', [MorningBriefingController::class, 'preview_briefing'])->name('preview-briefing');
            Route::post('/store-briefing', [MorningBriefingController::class, 'store'])->name('briefing.store');
            Route::get('/fetch-briefing-data/{itemId}', [MorningBriefingController::class, 'show'])->name('briefing.show');
            Route::put('/update-briefing/{itemId}', [MorningBriefingController::class, 'update'])->name('briefing.update');
            Route::delete('/marketing-briefing/delete-item/{id}', [MorningBriefingController::class, 'delete_briefing'])->name('delete-briefing');
        });


        Route::middleware(['checkUserAccess:205', 'suspiciousTexts'])->group(function () {
            Route::get('/qr-code-generator', [BarcodeController::class, 'index'])->name('barcode_page');
            Route::post('/generate-qr-code', [BarcodeController::class, 'generateQR'])->name('generate.Qr');
        });
    });

    Route::middleware(['suspiciousTexts'])->group(function () {
        //alowed method
        Route::get('/manage-allowed-method', [ManageAccessController::class, 'manage_request'])->name('manage.request');
        Route::post('/assign-role-to-methods', [ManageAccessController::class, 'grant_method_access_to_roles'])->name('assign.roles.to.method');
        Route::get('/reset-methods-access/{id}', [ManageAccessController::class, 'remove_methods_access'])->name('remove.method.access');
    });
});

require __DIR__.'/auth.php';
