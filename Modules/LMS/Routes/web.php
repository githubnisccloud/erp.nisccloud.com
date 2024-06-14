<?php

use Illuminate\Support\Facades\Route;
use Modules\LMS\Http\Controllers\BlogController;
use Modules\LMS\Http\Controllers\CategoryController;
use Modules\LMS\Http\Controllers\ChaptersController;
use Modules\LMS\Http\Controllers\CourseController;
use Modules\LMS\Http\Controllers\CourseCouponController;
use Modules\LMS\Http\Controllers\CourseFaqController;
use Modules\LMS\Http\Controllers\CourseOrderController;
use Modules\LMS\Http\Controllers\HeaderController;
use Modules\LMS\Http\Controllers\LMSController;
use Modules\LMS\Http\Controllers\PageOptionController;
use Modules\LMS\Http\Controllers\RattingController;
use Modules\LMS\Http\Controllers\StoreAnalyticController;
use Modules\LMS\Http\Controllers\StoreController;
use Modules\LMS\Http\Controllers\StudentForgotPasswordController;
use Modules\LMS\Http\Controllers\StudentlogController;
use Modules\LMS\Http\Controllers\StudentLoginController;
use Modules\LMS\Http\Controllers\SubcategoryController;
use Modules\LMS\Http\Controllers\SubscriptionController;

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

Route::group(['middleware' => 'PlanModuleCheck:LMS'], function ()
{
    // dashboard
    Route::get('dashboard/lms', [LMSController::class, 'index'])->middleware('auth')->name('lms.dashboard');

    // Report
    Route::get('storeanalytic', [StoreAnalyticController::class, 'index'])->middleware(['auth'])->name('storeanalytic');

    // course
    Route::resource('course', CourseController::class)->middleware(['auth']);
    Route::post('course/getsubcategory', [CourseController::class, 'getsubcategory'])->middleware(['auth'])->name('course.getsubcategory');

    Route::resource('course-category', CategoryController::class)->middleware(['auth']);
    Route::resource('course-subcategory', SubcategoryController::class)->middleware(['auth']);

    Route::resource('{id}/headers', HeaderController::class)->middleware(['auth']);

    Route::resource('{course_id}/{id}/chapters', ChaptersController::class)->middleware(['auth']);
    Route::post('chapters/{id}/update', [ChaptersController::class, 'ContentsUpdate'])->middleware(['auth'])->name('chapters.update');
    Route::delete('chapters/{id}/delete', [ChaptersController::class, 'fileDelete'])->middleware(['auth'])->name('chapters.file.delete');

    /*Practices*/
    Route::post('course/practices-files/{id}', [CourseController::class, 'practicesFiles'])->middleware(['auth'])->name('course.practicesfiles');
    Route::delete('course/practices-files/{id}/delete', [CourseController::class, 'fileDelete'])->middleware(['auth'])->name('practices.file.delete');
    Route::get('course/practices-files-name/{id}/file-name', [CourseController::class, 'editFileName'])->middleware(['auth'])->name('practices.filename.edit');
    Route::put('course/practices-files-update/{id}/file-name', [CourseController::class, 'updateFileName'])->middleware(['auth'])->name('practices.filename.update');

    //FAQs
    Route::resource('{id}/course-faqs', CourseFaqController::class)->middleware(['auth']);

    Route::put('course-seo-update/{id}/', [CourseController::class, 'CourseSeoUpdate'])->middleware(['auth'])->name('course.seo.update');

    Route::resource('custom-page', PageOptionController::class)->middleware(['auth']);

    Route::resource('blog', BlogController::class)->middleware(['auth']);
    Route::get('blog-social', [BlogController::class, 'socialBlog'])->middleware(['auth'])->name('blog.social');
    Route::get('store-social-blog', [BlogController::class, 'storeSocialblog'])->middleware(['auth'])->name('store.socialblog');

    Route::resource('subscriptions', SubscriptionController::class)->middleware(['auth']);

    Route::resource('course-coupon', CourseCouponController::class)->middleware(['auth']);

    // settings
    Route::post('lms/store_setting', [LMSController::class, 'LmsStoreSetting'])->name('lms.store.setting');
    Route::post('lms-store', [LMSController::class, 'changeTheme'])->name('store.changetheme');
    Route::get('{slug?}/edit-products/{theme?}', [LMSController::class, 'Editproducts'])->middleware(['auth'])->name('store.editproducts');
    Route::post('{slug?}/store-edit-products/{theme?}', [LMSController::class, 'StoreEditProduct'])->middleware(['auth'])->name('store.storeeditproducts');
    Route::post('product-image-delete', [StoreController::class, 'image_delete'])->name('product.image.delete')->middleware(['auth']);
    Route::post('/certificate/template/setting', [LMSController::class, 'saveCertificateSettings'])->name('certificate.template.setting');
    Route::get('/certificate/preview/{template}/{color}/{gradiant?}', [LMSController::class, 'previewCertificate'])->middleware(['auth',])->name('certificate.preview');

    // Course order show
    Route::resource('course_orders', CourseOrderController::class)->middleware(['auth']);

    // student view
    Route::get('student', [StoreController::class, 'studentindex'])->name('student.index')->middleware(['auth']);
    Route::get('student/view/{id}', [StoreController::class, 'studentShow'])->name('student.show')->middleware(['auth']);

    // Student Logs
    Route::get('student-logs', [StudentlogController::class, 'index'])->middleware(['auth'])->name('student.logs');
    Route::get('student-logs/show/{id}', [StudentlogController::class, 'show'])->middleware(['auth'])->name('studentlog.show');
    Route::delete('student-logs/delete/{id}', [StudentlogController::class, 'destroy'])->middleware(['auth'])->name('studentlog.destroy');
});

Route::get('store-lms/{slug?}', [StoreController::class, 'storeSlug'])->name('store.slug');
Route::get('{slug?}/view-course/{id}', [StoreController::class, 'ViewCourse'])->name('store.viewcourse');
Route::get('{slug}/checkout/{id}/{total}', [StoreController::class, 'checkout'])->name('store.checkout');
Route::get('change-language-store/{slug?}/{lang}', [StoreController::class, 'changeLanquageStore'])->name('change.languagestore');
Route::get('{slug?}/tutor/{id}', [StoreController::class, 'tutor'])->name('store.tutor');
Route::get('{slug}/fullscreen/{course}/{id?}/{type?}', [StoreController::class, 'fullscreen'])->middleware(['studentAuth'])->name('store.fullscreen');

// Ratting
Route::resource('rating', RattingController::class);
Route::get('rating/{slug?}/product/{id}', [RattingController::class,'rating'])->name('rating');
Route::post('store_rating/{slug?}/product/{course_id}/{tutor_id}', [RattingController::class,'store_rating'])->name('store_rating');

// Search
Route::get('{slug?}/search/{search?}/{category?}', [StoreController::class, 'search'])->name('store.search');
Route::post('{slug?}/filter', [StoreController::class, 'filter'])->name('store.filter');
Route::get('{slug?}/search-data/{search}', [StoreController::class, 'searchData'])->name('store.searchData');

// cart
Route::post('add-to-cart/{slug?}/{id}/{variant_id?}', [StoreController::class, 'addToCart'])->name('user.addToCart');
Route::get('user-cart-item/{slug?}/cart', [StoreController::class, 'StoreCart'])->name('store.cart');
Route::POST('subscriptions/{id}', [SubscriptionController::class,'store_email'])->name('subscriptions.store_email');
Route::delete('delete_cart_item/{slug?}/product/{id}/{variant_id?}', [StoreController::class, 'delete_cart_item'])->name('delete.cart_item');

// Wishlist
Route::post('{slug}/student-addtowishlist/{id}', [StoreController::class, 'wishlist'])->name('student.addToWishlist');
Route::get('{slug}/student-wishlist', [StoreController::class, 'wishlistpage'])->middleware(['studentAuth'])->name('student.wishlist');
Route::post('{slug}/student-removefromwishlist/{id}', [StoreController::class, 'removeWishlist'])->middleware(['studentAuth'])->name('student.removeFromWishlist');

//  view course

// student
Route::post('{slug}/student-login/{cart?}', [StudentLoginController::class, 'login'])->name('student.login');
Route::get('{slug}/student-login', [StudentLoginController::class, 'showLoginForm'])->name('student.loginform');

//Forgot Password
Route::get('{slug}/student-password/',[StudentForgotPasswordController::class, 'showLinkRequestForm'])->name('student.password.request');
Route::post('{slug}/student-password/email',[StudentForgotPasswordController::class, 'postStudentEmail'])->name('student.password.email');

// reset password
Route::get('{slug}/student-password/reset/{token}',[StudentForgotPasswordController::class, 'getStudentPassword'])->name('student.password.reset');
Route::post('{slug}/student-password/reset',[StudentForgotPasswordController::class, 'updateStudentPassword'])->name('student.password.update');

Route::get('/{slug}/student-profile/{id}',[StudentLoginController::class, 'profile'])->middleware('studentAuth')->name('student.profile');
Route::post('{slug}/student-logout', [StudentLoginController::class, 'logout'])->name('student.logout');
Route::put('{slug}/student-profile/{id}',[StudentLoginController::class, 'profileUpdate'])->middleware('studentAuth')->name('student.profile.update');
Route::get('{slug}/user-create', [StoreController::class, 'userCreate'])->name('store.usercreate');
Route::post('{slug}/user-create', [StoreController::class, 'userStore'])->name('store.userstore');
Route::get('{slug}/student-home', [StoreController::class, 'studentHome'])->middleware(['studentAuth'])->name('student.home');

// Blog
Route::get('store-blog/{slug?}', [StoreController::class, 'StoreBlog'])->name('store.blog');
Route::get('store-blog-view/{slug?}/blog/{id}', [StoreController::class, 'StoreBlogView'])->name('store.store_blog_view');

// custom page
Route::get('page/{slug?}/{store_slug?}', [StoreController::class, 'pageOptionSlug'])->name('pageoption.slug');

// Course Coupon
Route::get('/apply-coursecoupon', [CourseCouponController::class, 'applyCourseCoupon'])->name('apply.coursecoupon');

Route::get('store-complete/{slug?}/{id}', [StoreController::class, 'complete'])->name('store-complete.complete');
Route::get('{slug?}/order/{id}', [StoreController::class, 'userorder'])->name('user.order');

/*CHECKBOX*/
Route::post('student-watched/{id}/{course_id}/{slug?}', [StoreController::class, 'checkbox'])->name('student.checkbox');
Route::post('student-remove-watched/{id}/{course_id}/{slug?}', [StoreController::class, 'removeCheckbox'])->name('student.remove.checkbox');

//==================================== Download button ====================================//
Route::get('certificate/pdf/{course_id}/{id}', [StoreController::class, 'certificatedl'])->name('certificate.pdf');

// bank transfer
Route::post('{slug?}/bank_transfer', [StoreController::class, 'coursePayWithBank'])->name('course.pay.with.bank');
Route::get('course-bank-request/{id}', [StoreController::class, 'courseBankRequestEdit'])->name('course.bank.request.edit')->middleware(['auth']);
Route::post('course-bank-request-edit/{id}',[StoreController::class, 'courseBankRequestupdate'])->name('course.bank.request.update')->middleware(['auth']);
