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
Route::group(['middleware' => 'PlanModuleCheck:DoubleEntry'], function ()
{

    Route::prefix('doubleentry')->group(function() {
        Route::get('/', 'DoubleEntryController@index');
    });


    Route::resource('journal-entry', 'JournalEntryController')->middleware(['auth']);
    Route::post('journal-entry/account/destroy', 'JournalEntryController@accountDestroy')->name('journal.account.destroy')->middleware(['auth']);
    Route::delete('journal-entry/journal/destroy/{item_id}', 'JournalEntryController@journalDestroy')->name('journal.destroy')->middleware(['auth']);

    Route::get('report/ledger/{account?}', 'ReportController@ledgerReport')->name('report.ledger');

    Route::get('report/balance-sheet/{view?}', 'ReportController@balanceSheet')->name('report.balance.sheet');

    Route::get('report/profit-loss/{view?}', 'ReportController@profitLoss')->name('report.profit.loss');

    Route::get('report/trial-balance', 'ReportController@trialBalance')->name('report.trial.balance');

    Route::get('report/sales', 'ReportController@salesReport')->name('report.sales');
    Route::post('print/sales-report', 'ReportController@salesReportPrint')->name('sales.report.print');

    Route::get('report/receivables', 'ReportController@ReceivablesReport')->name('report.receivables');
    Route::post('print/receivables', 'ReportController@ReceivablesPrint')->name('receivables.print');




    Route::get('report/payables', 'ReportController@PayablesReport')->name('report.payables');
    Route::post('print/payables', 'ReportController@PayablesPrint')->name('payables.print');


    Route::post('journal-entry/setting/store', 'JournalEntryController@setting')->name('journal-entry.setting.store')->middleware(['auth']);





});
