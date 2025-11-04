<?php

use Illuminate\Support\Facades\Route;
use App\Models\Gong;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\PresenterAuthController;
use App\Http\Controllers\PresenterDashboardController;
use App\Http\Controllers\ContentFormController;

Route::get('/', function () {
    return view('welcome');
});

// Test routes for PDF generation (remove in production)
Route::get('/test-pdf/gong/{gong}', function (Gong $gong) {
    $pdf = Pdf::loadView('pdf.gong', [
        'gong' => $gong,
        'printedBy' => 'Test User (Admin)'
    ]);
    return $pdf->stream('test-gong.pdf');
})->name('test.gong.pdf');

Route::get('/test-pdf/advertisement/{advertisement}', function (\App\Models\Advertisement $advertisement) {
    $pdf = Pdf::loadView('pdf.advertisement', [
        'advertisement' => $advertisement,
        'printedBy' => 'Test User (Admin)'
    ]);
    return $pdf->stream('test-advertisement.pdf');
})->name('test.advertisement.pdf');

// Presenter Authentication Routes
Route::prefix('presenter')->group(function () {
    // Login routes
    Route::get('/login', [PresenterAuthController::class, 'showLoginForm'])->name('presenter.login.form');
    Route::post('/login', [PresenterAuthController::class, 'login'])->name('presenter.login');
    Route::post('/logout', [PresenterAuthController::class, 'logout'])->name('presenter.logout');

    // Dashboard routes (protected by presenter auth)
    Route::middleware(['presenter.auth', 'presenter.activity'])->group(function () {
        Route::get('/dashboard', [PresenterDashboardController::class, 'index'])->name('presenter.dashboard');
        Route::post('/mark-as-read', [PresenterDashboardController::class, 'markAsRead'])->name('presenter.mark-read');
        Route::post('/mark-as-unread', [PresenterDashboardController::class, 'markAsUnread'])->name('presenter.mark-unread');
        Route::get('/check-new-content', [PresenterDashboardController::class, 'checkNewContent'])->name('presenter.check-new-content');
        Route::get('/current-time-info', [PresenterDashboardController::class, 'getCurrentTimeInfo'])->name('presenter.current-time-info');
        Route::get('/time-slot-content', [PresenterDashboardController::class, 'getTimeSlotContent'])->name('presenter.time-slot-content');
        Route::get('/debug-readings', [PresenterDashboardController::class, 'debugReadings'])->name('presenter.debug-readings');
        Route::get('/reload-section/{sectionType}', [PresenterDashboardController::class, 'reloadSection'])->name('presenter.reload-section');

        // Content Form Routes
        Route::post('/content-form/tick', [ContentFormController::class, 'tick'])->name('presenter.content-form.tick');
        Route::post('/content-form/untick', [ContentFormController::class, 'untick'])->name('presenter.content-form.untick');
        Route::get('/content-form/{id}', [ContentFormController::class, 'show'])->name('presenter.content-form.show');
        Route::get('/content-forms', [ContentFormController::class, 'getPresenterForms'])->name('presenter.content-forms');
    });
});
