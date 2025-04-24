<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LearningPathController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\TutorController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/learning-path', [LearningPathController::class, 'generatePath']);
    Route::post('/feedback', [FeedbackController::class, 'getFeedback']);
    Route::post('/tutor', [TutorController::class, 'askTutor']);

});

Route::get('/test-ai', function(App\Services\AIService $aiService) {
    return $aiService->testConnection();
});

require __DIR__.'/auth.php';
