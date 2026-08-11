<?php

use App\Http\Controllers\Admin\ContentSectionController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\PeriodController;
use App\Http\Controllers\Admin\PrivateFileController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\SubmissionController as AdminSubmissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Public\ApplicantPortalController;
use App\Http\Controllers\Public\ChunkUploadController;
use App\Http\Controllers\Public\LandingController;
use App\Http\Controllers\Public\LegalController;
use App\Http\Controllers\Public\RegistrationController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', LandingController::class)->name('home');
Route::get('/daftar', [RegistrationController::class, 'create'])->name('registration.create');
Route::post('/registration/drafts', [RegistrationController::class, 'store'])->middleware('throttle:10,1')->name('registration.store');
Route::post('/registration/uploads/init', [ChunkUploadController::class, 'init'])->middleware('throttle:20,1')->name('uploads.init');
Route::post('/registration/uploads/{upload}/chunk', [ChunkUploadController::class, 'chunk'])->middleware('throttle:120,1')->name('uploads.chunk');
Route::post('/registration/uploads/{upload}/complete', [ChunkUploadController::class, 'complete'])->middleware('throttle:20,1')->name('uploads.complete');
Route::delete('/registration/uploads/{upload}', [ChunkUploadController::class, 'cancel'])->middleware('throttle:20,1')->name('uploads.cancel');
Route::get('/pendaftaran/berhasil/{submission}', [RegistrationController::class, 'success'])->middleware('signed')->name('registration.success');
Route::get('/tracking', fn () => Inertia::render('Applicant/RequestLink'))->name('applicant.request');
Route::post('/tracking/magic-link', [ApplicantPortalController::class, 'requestLink'])->middleware('throttle:5,1')->name('applicant.magic-link');
Route::get('/portal/{submission}', [ApplicantPortalController::class, 'show'])->middleware('signed')->name('applicant.portal');
Route::post('/portal/{submission}/revision', [ApplicantPortalController::class, 'revise'])->middleware(['signed', 'throttle:10,1'])->name('applicant.revision');
Route::get('/legal/{type}', LegalController::class)->whereIn('type', ['terms', 'privacy'])->name('legal.show');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');
    Route::get('/submissions', [AdminSubmissionController::class, 'index'])->name('submissions.index');
    Route::get('/submissions/{submission}', [AdminSubmissionController::class, 'show'])->name('submissions.show');
    Route::patch('/submissions/{submission}/status', [AdminSubmissionController::class, 'status'])->name('submissions.status');
    Route::post('/submissions/{submission}/revision-requests', [AdminSubmissionController::class, 'requestRevision'])->name('submissions.revisions.store');
    Route::get('/files/{file}', PrivateFileController::class)->name('files.show');
    Route::get('/faqs', [FaqController::class, 'index'])->name('faqs.index');
    Route::post('/faqs', [FaqController::class, 'store'])->name('faqs.store');
    Route::put('/faqs/{faq}', [FaqController::class, 'update'])->name('faqs.update');
    Route::get('/content', [ContentSectionController::class, 'index'])->name('content.index');
    Route::put('/content/{section}', [ContentSectionController::class, 'update'])->name('content.update');
    Route::get('/periods', [PeriodController::class, 'index'])->name('periods.index');
    Route::put('/periods/{period}', [PeriodController::class, 'update'])->name('periods.update');
    Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');
    Route::post('/submissions/{submission}/assignments', [ReviewController::class, 'assign'])->name('reviews.assign');
    Route::get('/reviews/{assignment}', [ReviewController::class, 'edit'])->name('reviews.edit');
    Route::put('/reviews/{assignment}', [ReviewController::class, 'score'])->name('reviews.score');
    Route::post('/submissions/{submission}/decision', [ReviewController::class, 'decide'])->name('reviews.decide');
    Route::get('/reports/{period}/csv', [ReportController::class, 'export'])->name('reports.csv');
});

require __DIR__.'/auth.php';
