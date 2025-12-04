<?php

use Illuminate\Support\Facades\Route;
use App\Models\Gong;
use Barryvdh\DomPDF\Facade\Pdf;

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


