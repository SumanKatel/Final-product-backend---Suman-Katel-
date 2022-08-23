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

Route::get('/c', function() {
    $exitCode = Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    return "All cleared";
});

Route::group(['namespace' => 'Site'], function (){
	Route::get('/','HomeController@index' )->name('index');
});




// Admin Web Route
Route::group(['prefix' => 'my-admin','namespace' => 'admin'], function (){
	Route::get('login', 'AdminLoginController@login')->name('login.page');
	Route::get('newsletter/{id}', 'AdminNewsletterController@show')->name('newsletterdetail');
	Route::post('login', 'AdminLoginController@loginCheck')->name('logincheck');
});

Route::group(['prefix' => 'u-admin', 'namespace' => 'admin', 'middleware'   => ['adminlogincheck','roles']], function (){
	Route::get('registeruser', 'AdminLoginController@userRegister')->name('user.create');
	Route::post('registeruser', 'AdminLoginController@userRegisterData')->name('userregister');
	Route::get('dashboard', 'AdminDashboardController@dashboard')->name('dashboard');
	Route::get('user/list', 'AdminLoginController@adminUserList')->name('user.list');
	Route::get('user/{id}/edit', 'AdminLoginController@editUser')->name('user.edit');
	Route::any('updateuser/{id}', 'AdminLoginController@updateuser')->name('user.update');
	Route::get('user/delete/{id}', ['as' => 'user.delete', 'uses' => 'AdminLoginController@deleteUser']);	
	Route::any('logout', 'AdminLoginController@logout')->name('logout');

	Route::any('updateuserprofile/{id}', 'AdminProfileController@updateuser')->name('userprofile.update');
	Route::get('userprofile/{id}/edituserprofile', 'AdminProfileController@editUserProfile')->name('userprofile.editprofile');

	Route::get('success-login', 'AdminSiteSettingController@successLogin')->name('successlogin');
	Route::get('fail-login', 'AdminSiteSettingController@failLogin')->name('faillogin');
	Route::get('menu', 'AdminMenuController@index')->name('menu');

// FAQ
	Route::resource('faq', 'AdminFaqController');
	Route::get('faq/delete/{id}', ['as' => 'faq.delete', 'uses' => 'AdminFaqController@destroy']);
	// User Group
	Route::resource('usergroup', 'AdminGroupController');
	Route::get('usergroup/delete/{id}', ['as' => 'usergroup.delete', 'uses' => 'AdminGroupController@destroy']);

	// Role Access
	Route::resource('role-access', 'AdminRoleAccessController');
	Route::get('role-access/delete/{id}', ['as' => 'role-access.delete', 'uses' => 'AdminRoleAccessController@destroy']);
    Route::get('roleChangeAccess/{allowId}/{id}','AdminRoleAccessController@changeAccess');
    Route::get('setting','AdminSiteSettingController@setting')->name('setting');
    Route::post('setting-update','AdminSiteSettingController@updateSetting')->name('update.setting');

	// slider
	Route::resource('slider', 'AdminSliderController');
	Route::get('slider/delete/{id}', ['as' => 'slider.delete', 'uses' => 'AdminSliderController@destroy']);


	// others
	Route::get('medialibrary', 'AdminDashboardController@mediaLibrary')->name('medialibrary');
	Route::any('ajax/drag-drop-sorting', 'AdminAjaxController@postDragDropSorting')->name('ajax.sorting');
	Route::any('module-url','AdminAjaxController@moduleUrl')->name('moduleUrl');


	// Contact
	Route::resource('contact', 'AdminContactController');
	Route::get('contact/delete/{id}', ['as' => 'contact.delete', 'uses' => 'AdminContactController@destroy']);

    Route::get('customer-list', 'AdminContactController@adminCustomerList')->name('adminCustomerList');
    Route::get('booked-list', 'AdminContactController@adminCustomerBookList')->name('adminCustomerBookList');
    Route::get('brochure-download-list', 'AdminContactController@adminCustomerBrochureList')->name('adminCustomerBrochureList');

	// Core Values
	Route::resource('core-value', 'CoreValueController');
	Route::get('core-value/delete/{id}', ['as' => 'core-value.delete', 'uses' => 'CoreValueController@destroy']);

	// category
	Route::resource('category', 'AdminCategoryController');
	Route::get('category/delete/{id}', ['as' => 'category.delete', 'uses' => 'AdminCategoryController@destroy']);

	// training
	Route::resource('training', 'TrainingDetailController');
	Route::get('training/delete/{id}', ['as' => 'training.delete', 'uses' => 'TrainingDetailController@destroy']);
	
	// product
	Route::resource('product', 'ProductController');
	Route::get('product/delete/{id}', ['as' => 'product.delete', 'uses' => 'ProductController@destroy']);

	Route::get('product/sub-product/{id}', 'ProductController@viewSubProduct')->name('viewSubProduct');

	Route::post('subProduct/update/{id}','ProductController@subProductUpdate')->name('subProductUpdate');

	Route::Post('product/sub-product/store/{id}','ProductController@subProductAdd')->name('subProductAdd');

	Route::get('product/sub-product/delete/{id}','ProductController@subProductDelete')->name('subProductDelete');

	Route::get('product/sub-product/Edit/{productId}/{id}','ProductController@subProductEdit')->name('subProductEdit');

	Route::get('customer', 'AdminCustomerController@index')->name('customer.index');
	Route::get('customer/{id}', 'AdminCustomerController@show')->name('customer.show');


	

});