<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BranchlessController;
use App\Http\Controllers\BranchlessLogController;
use App\Http\Controllers\StatusController;
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



Route::middleware(['auth'])->group(function () {

     // Dashboard (hidden) - redirect to nominatif branchless
     Route::get('/status', function () {
         return redirect()->route('branchless.pergantian');
     })->name('status');
     Route::get('/status/chart-data', [StatusController::class, 'chartData']);
    
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/branchless/register', function () {
        return view('branchless.register');
    })->name('branchless.register');

   
    Route::get('/branchless/register', [BranchlessController::class, 'create'])->name('branchless.register');
    Route::post('/branchless/register', [BranchlessController::class, 'store'])->name('branchless.register.submit');

    
    Route::get('/branchless/pergantian', [BranchlessController::class, 'index'])->name('branchless.pergantian');
    Route::get('/branchless/template', [BranchlessController::class, 'downloadTemplate'])->name('branchless.template');
    Route::post('/branchless/import', [BranchlessController::class, 'import'])->name('branchless.import');
    Route::post('/branchless/generate/{kode}', [App\Http\Controllers\BranchlessGenerateController::class, 'generate'])->name('branchless.generate');
    Route::post('/branchless/delete/{id}', [BranchlessController::class, 'destroy'])->name('branchless.delete');
    Route::get('/branchless/edit/{id}', [BranchlessController::class, 'edit'])->name('branchless.edit');
   // Route::post('/branchless/update/{id}', [BranchlessController::class, 'update'])->name('branchless.update');
    Route::put('/branchless/update/{id}', [BranchlessController::class, 'update'])->name('branchless.update');


    Route::get('/branchless/log', [BranchlessLogController::class, 'index'])->name('branchless.log');
    
    Route::get('/branchless/export', [BranchlessController::class, 'export'])->name('branchless.export');

    Route::get('/branchless/log/export', [BranchlessLogController::class, 'export'])->name('branchless.log.export');
    

    Route::post('/branchless/store', [BranchlessController::class, 'store'])->name('branchless.store');




});






Route::get('/', function () {
    return redirect()->route('login');
});



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
});

require __DIR__.'/auth.php';
