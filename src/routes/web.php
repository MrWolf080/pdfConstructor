<?php

use App\Http\Controllers\MainController;
use Illuminate\Support\Facades\Route;

Route::get('/', [MainController::class, 'index'])->name('home');
Route::post('/', [MainController::class, 'getFormData'])->name('formReciever');
Route::get('/formHistory/{id}', [MainController::class, 'getFormHistory'])->name('formHistory');
Route::get('/formHistory/delete/{id}', [MainController::class, 'deleteFormHistory'])->name('formHistoryDelete');
//Route::get('/', function () {
  //  return view('welcome');
//});
