<?php

declare(strict_types=1);

use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('report')->group(function (): void {
    Route::get('{code}/pdf', [ReportController::class, 'pdf'])->name('report.pdf');
    Route::get('{code}/docx', [ReportController::class, 'docx'])->name('report.docx');
});
