<?php

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
use Illuminate\Support\Facades\Route;
use Modules\Contract\Http\Controllers\ContractController;
use Modules\Contract\Http\Controllers\ContractTypeController;

Route::group(['middleware' => 'PlanModuleCheck:Contract'], function ()
{
    Route::resource('contract_type', ContractTypeController::class)->middleware(['auth']);
    Route::resource('contract', ContractController::class)->middleware(['auth']);
    Route::get('contract-grid', [ContractTypeController::class,'grid'])->name('contract.grid');
    Route::post('/contract_status_edit/{id}', [ContractTypeController::class,'contract_status_edit'])->name('contract.status')->middleware(['auth']);

    Route::get('/contract/copy/{id}', [ContractController::class,'copycontract'])->name('contracts.copy')->middleware(['auth']);
    Route::post('/contract/copy/store/{id}', [ContractController::class,'copycontractstore'])->name('contracts.copy.store')->middleware(['auth']);

    Route::post('contract/{id}/description', [ContractController::class,'descriptionStore'])->name('contracts.description.store')->middleware(['auth']);
    Route::post('/contract/{id}/file', [ContractController::class,'fileUpload'])->name('contracts.file.upload')->middleware(['auth']);
    Route::get('/contract/{id}/file/{fid}', [ContractController::class,'fileDownload'])->name('contracts.file.download')->middleware(['auth']);
    Route::delete('/contract/{id}/file/delete/{fid}', [ContractController::class,'fileDelete'])->name('contracts.file.delete')->middleware(['auth']);
    Route::post('/contract/{id}/comment', [ContractController::class,'commentStore'])->name('contract.comment.store');
    Route::get('/contract/{id}/comment', [ContractController::class,'commentDestroy'])->name('contract.comment.destroy');
    Route::post('/contract/{id}/note', [ContractController::class,'noteStore'])->name('contracts.note.store')->middleware(['auth']);
    Route::get('/contract/{id}/note', [ContractController::class,'noteDestroy'])->name('contracts.note.destroy')->middleware(['auth']);

    Route::get('contract/{id}/get_contract', [ContractController::class,'printContract'])->name('get.contract');
    Route::get('contract/pdf/{id}', [ContractController::class,'pdffromcontract'])->name('contract.download.pdf');

    Route::get('/signature/{id}', [ContractController::class,'signature'])->name('signature')->middleware(['auth']);
    Route::post('/signaturestore', [ContractController::class,'signatureStore'])->name('signaturestore')->middleware(['auth']);

    Route::get('/contract/{id}/mail', [ContractController::class,'sendmailContract'])->name('send.mail.contract');

    Route::post('contract/setting/store', [ContractController::class,'setting'])->name('contract.setting.store')->middleware(['auth']);

    Route::post('/getproject', [ContractController::class,'getProject'])->name('getproject');
});
