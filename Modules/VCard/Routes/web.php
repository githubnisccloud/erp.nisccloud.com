<?php

use Illuminate\Support\Facades\Route;
use Modules\VCard\Http\Controllers\VCardController;
use Modules\VCard\Http\Controllers\BusinessController;
use Modules\VCard\Http\Controllers\AppointmentDetailController;
use Modules\VCard\Http\Controllers\ContactsController;

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
Route::group(['middleware' => 'PlanModuleCheck:VCard'], function () {
    Route::middleware(['auth', 'verified'])->group(function () {
        Route::prefix('vcard')->group(function () {
            Route::get('/', 'VCardController@index');
        });
    });

    //dashboard
    Route::get('dashboard/vcard', [VCardController::class, 'index'])->name('dashboard.vcard');
    //Business
    Route::resource('business', BusinessController::class);
    Route::post('business/edit-theme/{id}', [BusinessController::class, 'editTheme'])->name('business.edit-theme');
    Route::post('business/domain-setting/{id}', [BusinessController::class, 'domainsetting'])->name('business.domain-setting');
    Route::post('business/block-setting/{id}', [BusinessController::class, 'blocksetting'])->name('business.block-setting');
    Route::post('business/cookie/{id}}', [BusinessController::class, 'saveCookiesetting'])->name('business.cookie-setting');
    Route::post('business/custom_qrcode/{id}', [BusinessController::class, 'saveCustomQrsetting'])->name('business.qrcode_setting');
    Route::post('business/pwa/{id}', [BusinessController::class, 'savePWA'])->name('business.pwa-setting');
    Route::get('business/{slug}/get_card', [BusinessController::class, 'cardpdf'])->name('get.card');
    Route::get('businessqr/download/', [BusinessController::class, 'downloadqr'])->name('download.qr');
    Route::post('business/destroy/', [BusinessController::class, 'destroyGallery'])->name('destory.gallery');
    Route::post('business/seo/{id}', [BusinessController::class, 'saveseo'])->name('business.seo-setting');
    Route::post('business/status/{id}', [BusinessController::class, 'ChangeStatus'])->name('business.status');
    Route::get('business/current/{id}', [BusinessController::class, 'ChangeBusiness'])->name('business.current');
    Route::get('business/grid/view', [BusinessController::class, 'Grid'])->name('business.grid.view');
   
  //  Route::post('business/analytics/{id}', [BusinessController::class, 'analytics'])->name('business.analytics');


    //Pixel
    Route::get('pixel/create/{id}', [BusinessController::class,'pixel_create'])->name('pixel.create');
    Route::post('pixel', [BusinessController::class,'pixel_store'])->name('pixel.store');
    Route::post('pixel-delete/{id}', [BusinessController::class,'pixeldestroy'])->name('pixel.destroy');

    //Appointment
    Route::resource('appointment', AppointmentDetailController::class);
    Route::get('card-appointment/{slug?}', [AppointmentDetailController::class ,'index'])->name('appointment.index');
    Route::get('appointment-calender', [AppointmentDetailController::class ,'appointmentCalender'])->name('appointment.calendar');
    Route::get('/appointment-note/{id?}', [AppointmentDetailController::class ,'add_note'])->name('appointment.add-note');
    Route::post('/appointment-note-store/{id?}', [AppointmentDetailController::class ,'note_store'])->name('appointment.note.store');
    Route::get('get-appointment-detail/{id}', [AppointmentDetailController::class ,'getAppointmentDetails'])->name('appointment.details');

    //Contact
    Route::get('/contacts/show', [ContactsController::class,'index'])->name('contacts.index');
    Route::get('/contact-note/{id?}', [ContactsController::class,'add_note'])->name('contact.add-note');
    Route::post('/contact-note-store/{id?}', [ContactsController::class,'note_store'])->name('contact.note.store');
    Route::get('/contacts/business/show{id}', [ContactsController::class,'index'])->name('business.contacts.show');
    Route::post('/contacts/delete/{id}', [ContactsController::class,'destroy'])->name('contacts.destroy');

});
//Without Auth
Route::get('cards/{slug}', [BusinessController::class,'getcard'])->name('get.vcard');
Route::get('/download/{slug}', [BusinessController::class,'getVcardDownload'])->name('bussiness.save');
Route::any('appoinment/make-appointment', [AppointmentDetailController::class,'store'])->name('appoinment.store');
Route::post('/contacts/store/', [ContactsController::class,'store'])->name('contacts.store');
Route::get('card_cookie_consent', [BusinessController::class,'cardCookieConsent'])->name('card-cookie-consent');
