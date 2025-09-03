<?php

use Illuminate\Support\Facades\Route;
use Modules\Forms\Http\Controllers\Admin;
use Modules\Forms\Http\Controllers\Client;

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

Route::prefix('/admin/forms')->middleware('web', 'admin', 'permission')->group(function () {
    Route::get('/', [Admin\FormsController::class, 'index'])->name('admin.forms.index');
    Route::get('/create', [Admin\FormsController::class, 'create'])->name('admin.forms.create');
    Route::post('/store', [Admin\FormsController::class, 'store'])->name('admin.forms.store');
    Route::get('/{form}/edit', [Admin\FormsController::class, 'edit'])->name('admin.forms.edit');
    Route::post('/{form}/update', [Admin\FormsController::class, 'update'])->name('admin.forms.update');
    Route::get('/{form}/delete', [Admin\FormsController::class, 'destroy'])->name('admin.forms.destroy');

    Route::get('/fields/{field}/up', [Admin\FormsController::class, 'fieldUp'])->name('admin.forms.fields.up');
    Route::get('/fields/{field}/down', [Admin\FormsController::class, 'fieldDown'])->name('admin.forms.fields.down');
    Route::get('/fields/{field}/delete', [Admin\FormsController::class, 'destroyField'])->name('admin.forms.fields.destroy');
    Route::post('/fields/{form}/store', [Admin\FormsController::class, 'storeField'])->name('admin.forms.fields.store');
    Route::post('/fields/{field}/update', [Admin\FormsController::class, 'updateField'])->name('admin.forms.fields.update');

    Route::get('/submissions', [Admin\FormsController::class, 'submissions'])->name('admin.forms.submissions.index');

});

Route::prefix(config('forms.route_prefix', 'forms'))->group(function(){
    Route::get('/{form:slug}', [Client\FormsController::class, 'view'])->name('forms.view');
    Route::post('/{form:slug}', [Client\FormsController::class, 'submit'])->name('forms.submit');
    Route::get('/submissions/{submission:token}', [Client\FormsController::class, 'viewSubmission'])->name('forms.view-submission');
    Route::post('/submissions/{submission:token}/pay', [Client\FormsController::class, 'paySubmission'])->name('forms.submissions.pay');

    Route::prefix('/actions')->middleware('web', 'admin', 'permission')->group(function () {
        Route::post('/submissions/{submission:token}/update', [Client\FormsController::class, 'updateSubmission'])->name('forms.submissions.update');
        Route::get('/submissions/{submission:token}/delete', [Client\FormsController::class, 'deleteSubmission'])->name('forms.submissions.delete');
    });
    
    Route::post('/submissions/{submission:token}/message', [Client\FormsController::class, 'postMessage'])->name('forms.view-submission.post-message');
});