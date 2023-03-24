<?php

use Illuminate\Support\Facades\Route;
use Modules\Schedule\Http\Controllers\TimingController;
use Modules\Schedule\Http\Controllers\ScheduleController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::resource('schedule', ScheduleController::class);
Route::get('schedule/{action}/{session}/{branch}/{class}/{section}/', [ScheduleController::class, 'actionFilter'])->name('schedule-filter-action');
Route::resource('timing', TimingController::class);
// Route::get('timing/search/{branch}', [TimingController::class, 'search'])->name('branch-fiter');