<?php

use Illuminate\Http\Request;

Route::post('/signup', 'Front\LoginController@frontSignup');
Route::post('/login', 'Front\LoginController@frontLogin');

//all products
Route::get('/products', 'Front\ProductController@allProduct');
Route::post('/compare', 'Front\ProductController@compare');
Route::post('/suggest', 'Front\ProductController@suggest');
Route::post('/valuation', 'Front\ProductController@valuation');
Route::post('brochure-download','Front\ProductController@brochureDownload' );
Route::post('feedback','Front\ProductController@feedback' );
Route::get('faq','Front\ProductController@faq' );

Route::group(['middleware' => 'frontApi'], function (){
//logout
Route::post('/logout', 'Front\LoginController@customerLogout');

//for Profile
Route::get('/profile', 'Front\ProfileController@getProfile');
Route::post('/book', 'Front\ProfileController@book');
Route::get('/book-list', 'Front\ProfileController@bookList');

Route::post('/update-profile', 'Front\ProfileController@updateProfile');
Route::post('/change-password', 'Front\ProfileController@updatePassword');

//wish list
Route::get('/wishes', 'Front\WishController@wishList');
Route::post('/wish/add', 'Front\WishController@wishAdd');
Route::post('/wish/delete/{id}', 'Front\WishController@wishDelete');

//Rating list
Route::get('/ratings', 'Front\WishController@ratingList');
Route::post('/rating/add', 'Front\WishController@ratingAdd');


//customer Cart
Route::get('/carts', 'Front\CartController@cartList');
Route::post('/cart/add', 'Front\CartController@cartAdd');
Route::get('/cart/delete/{id}', 'Front\CartController@cartDelete');

//product order
Route::post('/final/order', 'Front\OrderController@finalOrder');


///Order Details
Route::get('/orders', 'Front\UserOrderController@getAllOrder');


});
