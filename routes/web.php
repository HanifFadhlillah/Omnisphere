<?php

use App\Http\Controllers\ChangeRequestController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MilestoneOutputController;
use App\Http\Controllers\MilestoneOutputFulfillmentController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectDocumentController;
use App\Http\Controllers\ProjectMilestoneController;
use App\Http\Controllers\ProjectProgressController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// UC-01: Login/Logout ditangani oleh Laravel Breeze (routes/auth.php)
Route::redirect('/', '/dashboard');

Route::middleware('auth')->group(function () {

    // UC-02 + UC-10: Dashboard Monitoring & Reminder Deadline — semua role
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // UC-06: Halaman Change Request — semua role bisa lihat
    Route::get('/change-requests', [ChangeRequestController::class, 'index'])->name('change-requests.index');

    // Halaman item CR per proyek — semua role bisa lihat
    Route::get('/change-requests/{project}', [ChangeRequestController::class, 'show'])->name('change-requests.show');

    // UC-03: Kelola Data Proyek — Admin PMO & Staff PMO bisa CRUD, Viewer hanya index/show
    // PENTING: route literal (/projects/create) harus didaftarkan SEBELUM route
    // berparameter (/projects/{project}), kalau tidak Laravel akan mengira "create"
    // adalah nilai {project} dan berujung 404.
    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');

    Route::middleware('role:admin_pmo,staff_pmo')->group(function () {
        Route::get('/projects/create', [ProjectController::class, 'create'])->name('projects.create');
        Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
        Route::get('/projects/{project}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
        Route::put('/projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
        Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');



        // UC-04: Kelola Timeline Proyek
        Route::post('/projects/{project}/milestones', [ProjectMilestoneController::class, 'store'])->name('milestones.store');
        Route::put('/projects/{project}/milestones/{milestone}', [ProjectMilestoneController::class, 'update'])->name('milestones.update');
        Route::delete('/projects/{project}/milestones/{milestone}', [ProjectMilestoneController::class, 'destroy'])->name('milestones.destroy');

        // Kelola definisi output milestone (admin mendefinisikan output yang diharapkan)
        Route::post('/projects/{project}/milestones/{milestone}/outputs', [MilestoneOutputController::class, 'store'])->name('milestone-outputs.store');
        Route::put('/projects/{project}/milestones/{milestone}/outputs/{output}', [MilestoneOutputController::class, 'update'])->name('milestone-outputs.update');
        Route::delete('/projects/{project}/milestones/{milestone}/outputs/{output}', [MilestoneOutputController::class, 'destroy'])->name('milestone-outputs.destroy');

        // Edit fulfillment freetext (hanya admin)
        Route::put('/projects/{project}/milestones/{milestone}/outputs/{output}/fulfillments/{fulfillment}', [MilestoneOutputFulfillmentController::class, 'update'])->name('milestone-outputs.fulfillment.update');

        // UC-05: Monitoring Progres Proyek (input progres)
        Route::post('/projects/{project}/progress', [ProjectProgressController::class, 'store'])->name('progress.store');

        // UC-06: Kelola Change Request
        Route::post('/change-requests', [ChangeRequestController::class, 'store'])->name('change-requests.store');
        Route::put('/projects/{project}/change-requests/{changeRequest}', [ChangeRequestController::class, 'update'])->name('change-requests.update');
        Route::delete('/projects/{project}/change-requests/{changeRequest}', [ChangeRequestController::class, 'destroy'])->name('change-requests.destroy');

        // UC-07 + UC-08: Kelola & Upload Dokumen Proyek
        Route::post('/projects/{project}/documents', [ProjectDocumentController::class, 'store'])->name('documents.store');
        Route::delete('/projects/{project}/documents/{document}', [ProjectDocumentController::class, 'destroy'])->name('documents.destroy');

        // Submit fulfillment output milestone (admin + staff)
        Route::post('/projects/{project}/milestones/{milestone}/outputs/{output}/fulfill', [MilestoneOutputFulfillmentController::class, 'store'])->name('milestone-outputs.fulfill');

        // Ajukan validasi milestone (admin + staff)
        Route::post('/projects/{project}/milestones/{milestone}/submit-validation', [ProjectMilestoneController::class, 'submitValidation'])->name('milestones.submit-validation');
    });

    // Halaman detail proyek — didaftarkan SETELAH /projects/create agar tidak bentrok
    Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');

    // UC-09: Download Dokumen — semua role (Admin, Staff, Viewer)
    Route::get('/projects/{project}/documents/{document}/download', [ProjectDocumentController::class, 'download'])->name('documents.download');

    // Download file fulfillment output milestone — semua role
    Route::get('/projects/{project}/milestones/{milestone}/outputs/{output}/fulfillments/{fulfillment}/download', [MilestoneOutputFulfillmentController::class, 'download'])->name('milestone-outputs.fulfillment.download');

    // UC-11: Kelola Pengguna — hanya Administrator PMO
    Route::middleware('role:admin_pmo')->group(function () {
        Route::resource('users', UserController::class)->except(['show']);

        // Hapus fulfillment output milestone — hanya admin (staff boleh isi, tidak boleh hapus)
        Route::delete('/projects/{project}/milestones/{milestone}/outputs/{output}/fulfillments/{fulfillment}', [MilestoneOutputFulfillmentController::class, 'destroy'])->name('milestone-outputs.fulfillment.destroy');

        // Validasi (Approve), Tolak (Reject), & Batalkan Validasi Milestone — hanya Administrator PMO
        Route::post('/projects/{project}/milestones/{milestone}/approve', [ProjectMilestoneController::class, 'approveMilestone'])->name('milestones.approve');
        Route::post('/projects/{project}/milestones/{milestone}/validate', [ProjectMilestoneController::class, 'approveMilestone'])->name('milestones.validate');
        Route::post('/projects/{project}/milestones/{milestone}/reject', [ProjectMilestoneController::class, 'rejectMilestone'])->name('milestones.reject');
        Route::post('/projects/{project}/milestones/{milestone}/unvalidate', [ProjectMilestoneController::class, 'unvalidateMilestone'])->name('milestones.unvalidate');
    });

    // UC-12: Lihat Laporan Monitoring — semua role
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    // Profil pengguna (bawaan Breeze)
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [\App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
