<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AlertsController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\ControlDelivery;
use App\Http\Controllers\ControlUsers;
use App\Http\Controllers\CouponsController;
use App\Http\Controllers\PaymentsController;
use App\Http\Controllers\ProductImagesController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\RepliesController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\WarrantyAgentsController;
use Illuminate\Support\Facades\Route;


#### Admin Routes ####

Route::prefix('admin')->group(function () {

  Route::get('/', [AdminController::class, 'home'])->name('admin-dashboard');

  Route::get('login', [AdminController::class, 'adminLoginView'])->name('admin-login');
  Route::post('login', [AdminController::class, 'adminLoginAction']);

  Route::prefix('profile')->group(function () {

    Route::get('/', [AdminController::class, 'profileView'])->name('admin-profile');

    Route::get('update-password', [AdminController::class, 'changePasswordView'])->name('update-admin-password');
    Route::post('update-password', [AdminController::class, 'changePasswordAction']);

    Route::get('update-personal', [AdminController::class, 'changePersonalView'])->name('update-admin-personal');
    Route::post('update-personal', [AdminController::class, 'changePersonalAction']);

    Route::get('update-contact', [AdminController::class, 'changeContactView'])->name('update-admin-contact');
    Route::post('update-contact', [AdminController::class, 'changeContactAction']);

    Route::get('update-picture', [AdminController::class, 'changePictureView'])->name('update-admin-picture');
    Route::post('update-picture', [AdminController::class, 'changePictureAction']);

  });

  Route::prefix('coupons')->group(function () {

    Route::get('/', [CouponsController::class, 'couponsView'])->name('coupons-index');
    Route::post('/', [CouponsController::class, 'couponsActions']);

    Route::get('add', [CouponsController::class, 'couponsAddView'])->name('coupons-add-index');
    Route::post('add', [CouponsController::class, 'couponsAddAction']);

    Route::get('deleted-coupons', [CouponsController::class, 'deletedCouponsView'])->name('coupons-deleted-index');
    Route::post('deleted-coupons', [CouponsController::class, 'deletedCouponsAction']);

    Route::get('update/{coupon_id}', [CouponsController::class, 'couponsUpdateView'])->name('coupons-update-index');
    Route::post('update/{coupon_id}', [CouponsController::class, 'couponsUpdateAction']);

  });

  Route::prefix('products')->group(function () {
    Route::get('/', [ProductsController::class, 'productsView'])->name('products-index');
    Route::post('/', [ProductsController::class, 'productsActions']);


    Route::get('deleted', [ProductsController::class, 'deletedProductsView'])->name('products-deleted-index');
    Route::post('deleted', [ProductsController::class, 'productsActions']);

    Route::get('view/{product_id}', [ProductsController::class, 'viewProduct'])->name('view-product-index');

    Route::get('update/{product_id}', [ProductsController::class, 'updateProductView'])->name('products-update-index');
    Route::post('update/{product_id}', [ProductsController::class, 'updateProductAction']);

    Route::get('add', [ProductsController::class, 'productsView'])->name('products-add-index');
    Route::get('add', [ProductsController::class, 'addProductView'])->name('products-add-index');
    Route::post('add', [ProductsController::class, 'addProductAction']);

    Route::get('pictures-of/{product_id}/{product_name}/add', [ProductsController::class, 'picturesAddView'])->name('product-pictures-add-index');
    Route::post('pictures-of/{product_id}/{product_name}/add', [ProductImagesController::class, 'addProductPictures']);

    Route::get('pictures-of/{product_id}/{product_name}', [ProductsController::class, 'picsView'])->name('pictures-index');
    Route::post('pictures-of/{product_id}/{product_name}', [ProductsController::class, 'picsAction']);

  });

  Route::prefix('categories')->group(function () {

    Route::get('/', [CategoriesController::class, 'categories_index'])->name('categories-index');
    Route::post('/', [CategoriesController::class, 'categories_actions']);

    Route::get('update/{category_id}/{category_name}', [CategoriesController::class, 'category_update_index'])->name('category-update-index');
    Route::post('update/{category_id}/{category_name}', [CategoriesController::class, 'category_update_action']);

    Route::get('add-new-category', [CategoriesController::class, 'add_category_index'])->name('category-add-index');
    Route::post('add-new-category', [CategoriesController::class, 'add_category_action']);

    Route::get('deleted', [CategoriesController::class, 'deleted_categories_index'])->name('deleted-categories-index');
    Route::post('deleted', [CategoriesController::class, 'deleted_categories_actions']);

    Route::get('/sub-categories/{category_id}', [CategoriesController::class, 'sub_categories_index'])->name('sub-categories-index');
    Route::post('/sub-categories/{category_id}', [CategoriesController::class, 'sub_categories_actions']);

    Route::get('/sub-categories/update/{sub_id}/{category_id}', [CategoriesController::class, 'update_sub_index'])->name('sub-update-index');
    Route::post('/sub-categories/update/{sub_id}/{category_id}', [CategoriesController::class, 'update_sub_action']);

    Route::get('/sub-categories/add/{category_id}', [CategoriesController::class, 'add_sub_index'])->name('add-sub-index');
    Route::post('/sub-categories/add/{category_id}', [CategoriesController::class, 'add_sub_action']);

    Route::get('/sub-categories/deleted/{category_id}', [CategoriesController::class, 'sub_deleted_index'])->name('sub-deleted-index');
    Route::post('/sub-categories/deleted/{category_id}', [CategoriesController::class, 'sub_deleted_action']);

  });

  Route::prefix('payments')->group(function () {

    Route::get('/', [PaymentsController::class, 'paymentsIndex'])->name('payments-index');
    Route::post('/', [PaymentsController::class, 'paymentsActions']);

    Route::get('undelivered', [PaymentsController::class, 'undeliveredPaymentsIndex'])->name('payments-undelivered-index');
    Route::post('undelivered', [PaymentsController::class, 'undeliveredPaymentsActions']);

    Route::get('view-payment/{payment_id}', [PaymentsController::class, 'viewPaymentIndex'])->name('view-payment-index');

  });

  Route::prefix('admins')->group(function () {

    Route::get('/', [AdminController::class, 'adminsIndex'])->name('admins-index');
    Route::post('/', [AdminController::class, 'adminsActions']);

    Route::get('add', [AdminController::class, 'addAdminIndex'])->name('add-admin-index');
    Route::post('add', [AdminController::class, 'addAdminAction']);

  });

  Route::prefix('delivery')->group(function () {

    Route::get('/', [ControlDelivery::class, 'deliveryMenIndex'])->name('delivery-index');
    Route::post('/', [ControlDelivery::class, 'deliveryMenActions']);

    Route::get('add', [ControlDelivery::class, 'addDeliveryIndex'])->name('add-delivery-index');
    Route::post('add', [ControlDelivery::class, 'addDeliveryAction']);

    Route::get('update/{delivery_id}', [ControlDelivery::class, 'updateDeliveryIndex'])->name('update-delivery-index');
    Route::post('update/{delivery_id}', [ControlDelivery::class, 'updateDeliveryAction']);

    Route::get('view/{delivery_id}', [ControlDelivery::class, 'viewDeliveryIndex'])->name('view-delivery-index');

  });

  Route::prefix('users')->group(function () {

    Route::get('/', [ControlUsers::class, 'usersIndex'])->name('users-index');
    Route::post('/', [ControlUsers::class, 'usersActions']);

    Route::get('disabled', [ControlUsers::class, 'disabledUsersIndex'])->name('disabled-users-index');
    Route::post('disabled', [ControlUsers::class, 'disabledUsersActions']);

    Route::get('add', [ControlUsers::class, 'addUserIndex'])->name('add-user-index');
    Route::post('add', [ControlUsers::class, 'addUserAction']);

    Route::get('view/{user_id}', [ControlUsers::class, 'viewUserIndex'])->name('view-user-index');

    Route::get('payments/{user_id}', [ControlUsers::class, 'userPaymentsIndex'])->name('user-payments-index');
    Route::get('favs/{user_id}', [ControlUsers::class, 'userFavsIndex'])->name('user-favs-index');
    Route::get('addresses/{user_id}', [ControlUsers::class, 'userAddressesIndex'])->name('user-addresses-index');
    Route::get('reports/{user_id}', [ControlUsers::class, 'userReportsIndex'])->name('user-reports-index');
    Route::get('refunding_processes/{user_id}', [ControlUsers::class, 'userRefundingIndex'])->name('user-refunding-index');

  });

  Route::prefix('alerts')->group(function () {

    Route::get('/', [AlertsController::class, 'alertsIndex'])->name('alerts-index');
    Route::post('/', [AlertsController::class, 'alertsActions']);

    Route::get('send', [AlertsController::class, 'alertsAddIndex'])->name('alerts-add-index');
    Route::post('send', [AlertsController::class, 'alertsAddAction']);

  });

  Route::prefix('reports')->group(function () {
    Route::get('/', [ReportsController::class, 'reportsIndex'])->name('reports-index');
    Route::post('/', [ReportsController::class, 'reportsActions']);

    Route::get('reply/{report_id}', [ReportsController::class, 'reportReplyIndex'])->name('report-reply-index');
    Route::post('reply/{report_id}', [ReportsController::class, 'reportReplyAction']);

    Route::get('/replies-of/{report_id}', [RepliesController::class, 'repliesOfReportIndex'])->name('report-replies-index');
    Route::post('/replies-of/{report_id}', [RepliesController::class, 'repliesOfReportActions']);

  });

  Route::prefix('agents')->group(function () {

    Route::get('/', [WarrantyAgentsController::class, 'agentsIndex'])->name('agents-index');
    Route::post('/', [WarrantyAgentsController::class, 'agentsActions']);

    Route::get('add', [WarrantyAgentsController::class, 'addAgentIndex'])->name('agents-add-index');
    Route::post('add', [WarrantyAgentsController::class, 'addAgentAction']);

    Route::get('update/{agent_id}', [WarrantyAgentsController::class, 'agentUpdateIndex'])->name('agents-update-index');
    Route::post('update/{agent_id}', [WarrantyAgentsController::class, 'agentUpdateAction']);

    Route::get('products/{agent_id}', [WarrantyAgentsController::class, 'productsOfAgentIndex'])->name('agents-products-index');
    Route::post('products/{agent_id}', [WarrantyAgentsController::class, 'productsOfAgentActions']);

    Route::get('deleted', [WarrantyAgentsController::class, 'deletedAgentsIndex'])->name('agents-deleted-index');
    Route::post('deleted', [WarrantyAgentsController::class, 'deletedAgentsAction']);

  });

});

Route::get('/test', function () {
  return view('testing');
});

