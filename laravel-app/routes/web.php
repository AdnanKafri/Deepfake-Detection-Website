<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DeepfakeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AnalysisController;
use App\Http\Controllers\ReportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

# 🔹 الصفحة الرئيسية: تحليل تفاعلي
Route::get('/', [DeepfakeController::class, 'index'])->name('deepfake.index');
Route::post('/deepfake/analyze', [DeepfakeController::class, 'analyze'])->name('deepfake.analyze');

# 🔐 صفحات محمية للمستخدم
Route::middleware('auth')->group(function () {

    # 🔸 لوحة التحكم
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // routes/web.php
    Route::get('/analysis/{id}', [DashboardController::class, 'show'])->name('analysis.show');
    // routes/web.php
    Route::post('/analysis/{id}/report', [DashboardController::class, 'report'])->middleware('auth')->name('analysis.report');
    // routes/web.php
    Route::post('/analysis/{id}/feedback', [DashboardController::class, 'feedback'])->middleware('auth')->name('analysis.feedback');

    Route::get('/analysis/{id}/pdf', [ReportController::class, 'generate'])->name('analysis.pdf');

    Route::get('/analysis/{id}/image/{segment_index}', [ReportController::class, 'showImage'])->name('analysis.image');

    # 🔸 إدارة الحساب الشخصي
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    # 🔸 التحليلات
    Route::prefix('analyses')->name('analysis.')->group(function () {
        Route::get('/', [AnalysisController::class, 'index'])->name('index'); // ليس ضروري حالياً
        // Route::get('/{id}', [AnalysisController::class, 'show'])->name('show');
    });
});

# 🛡️ Auth scaffolding
require __DIR__.'/auth.php';


use App\Http\Controllers\AudioExtractController;

Route::get('/extract-audio', [AudioExtractController::class, 'index'])->name('audio.extract.index');
Route::post('/extract-audio/upload', [AudioExtractController::class, 'upload'])->name('audio.extract.upload');
Route::get('/extract-audio/status', [AudioExtractController::class, 'checkStatus'])->name('audio.extract.status');

// لوحة الإدارة (Admin)
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [\App\Http\Controllers\AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users', [\App\Http\Controllers\AdminController::class, 'users'])->name('users');
    Route::get('/analyses', [\App\Http\Controllers\AdminController::class, 'analyses'])->name('analyses');
    Route::get('/users/{id}', [\App\Http\Controllers\AdminController::class, 'userDetails'])->name('user.details');
    Route::get('/analyses/{id}', [\App\Http\Controllers\AdminController::class, 'analysisDetails'])->name('analysis.details');
    Route::put('/users/{id}/role', [\App\Http\Controllers\AdminController::class, 'updateUserRole'])->name('user.update-role');
    Route::delete('/users/{id}', [\App\Http\Controllers\AdminController::class, 'deleteUser'])->name('user.delete');
    Route::delete('/analyses/{id}', [\App\Http\Controllers\AdminController::class, 'deleteAnalysis'])->name('analysis.delete');
});
