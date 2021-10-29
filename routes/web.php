<?php

use App\Http\Controllers\AddressesController;
use App\Http\Controllers\AppController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\DeliveryMenController;
use App\Http\Controllers\FavsController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\RefundingController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ShoppingCartController;
use App\Http\Controllers\SubCategoriesController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\OrdersController;

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

Auth::routes([
  'verify' => true
]);


Route::get('new-profile', function () {
  return view('new-profile');
});
#############################################################################################################

                  #### Website Routes ####

# Home - Main Page
Route::get('/', function () {
  return view('home');
})->name('home-page');


# Shopping Cart Page
Route::get('/complete-shopping', function () {
  return view('complete-shopping');
})->middleware('auth')->name('shopping-cart');

# Favourites page
Route::get('/favourites', [FavsController::class, 'index'])->name('favourites');

#############################################################################################################

                  #### Categories Routes ####

Route::get('/category/{category_name}/{id}', [CategoriesController::class, 'view_category'])->name('view-category');
Route::get('/category/sub_category/{category_id}/{sub_category_name}/{sub_category_id}', [SubCategoriesController::class, 'index'])->name('sub-category-index');

#############################################################################################################
                 #### Search items Routes ####
Route::get('/search', [SearchController::class, 'view'])->name('search-page');

#############################################################################################################

                  #### Profile Routes ####

# Profile Page
Route::get('/profile', [UserController::class, 'index'])->name('user-profile')->middleware('verified');

# User Update information
Route::get('/profile/update', [UserController::class, 'update_view'])->name('update-account')->middleware('verified');

Route::get('/profile/update-password', [UserController::class, 'update_password_view'])->name('update-password')->middleware('verified');

# User Payments
Route::get('/profile/orders', [UserController::class, 'orders_view'])->name('account-orders')->middleware('verified');
Route::get('/profile/orders/view/{process_id}', [UserController::class, 'view_order_blade'])->name('view-order')->middleware('verified');

# User Secondary Addresses
Route::get('/profile/addresses', [UserController::class, 'addresses_view'])->name('account-addresses')->middleware('verified');

# Update Secondary Addresses
Route::get('/profile/addresses/update/{id}', [AddressesController::class, 'update_addresses_view'])->name('update-address')->middleware('verified');
Route::post('/profile/addresses/update/{id}', [AddressesController::class, 'update_address_action']);


# User Charged points
Route::get('/profile/points', [UserController::class, 'points_view'])->name('account-points')->middleware('verified');

# User Payments Processes
Route::get('/profile/payments', [UserController::class, 'payments_view'])->name('account-payments')->middleware('verified');

# Refund charged items
Route::get('/profile/refunding/products/requests', [RefundingController::class, 'refund_view'])->name('account-refund')->middleware('verified');
# New refund request
Route::get('/profile/refunding/products/requests/new', [RefundingController::class, 'refund_requests_view'])->name('new-refund')->middleware('verified');
Route::post('/profile/refunding/products/requests/new', [RefundingController::class, 'refund_new_action']);

#############################################################################################################

                  #### Products & Categories Routes ####

# Viewing product
Route::get('/view/{product_id}', [ProductsController::class, 'show_product_info'])->name('view-item');

# Buying Product
Route::get('/buy/{product_id}', [OrdersController::class, 'buy_page_view'])->name('buy-view');
Route::post('/buy/{product_id}', [OrdersController::class, 'place_order'])->name('apply-coupon');


#############################################################################################################

                #### Delivery Routes ####

# Main Page
Route::get('/delivery', [DeliveryMenController::class, 'index'])->name('delivery-dashboard');

Route::get('/delivery/messages', [DeliveryMenController::class, 'messages_view'])->name('delivery-messages');
Route::get('/delivery/delivered_orders', [DeliveryMenController::class, 'delivered_orders_view'])->name('delivered-orders');
Route::get('/delivery/delivered_orders/{payment_id}/{product_id}', [DeliveryMenController::class, 'payment_order_view'])->name('view-delivered-order');

Route::get('/delivery/pending_orders', [DeliveryMenController::class, 'pending_orders_view'])->name('pending-orders');
Route::post('/delivery/pending_orders', [DeliveryMenController::class, 'pending_orders_action']);

Route::get('/delivery/edit', [DeliveryMenController::class, 'edit_delivery_view'])->name('delivery-edit');
Route::post('/delivery/edit', [DeliveryMenController::class, 'edit_delivery_action']);

# Login Page
Route::get('/delivery-login', [DeliveryMenController::class, 'login_view'])->name('login-delivery');
Route::post('/delivery-login', [DeliveryMenController::class, 'login']);

Route::get('/delivery/logout', function () {
  return view('delivery-men.logout');
})->name('delivery-logout');
