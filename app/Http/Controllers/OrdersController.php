<?php

namespace App\Http\Controllers;

use App\Models\Coupons;
use App\Models\Payments;
use App\Models\Products;
use Illuminate\Http\Request;

class OrdersController extends Controller
{

  public function __construct() {
    $this->middleware('auth');
  }

  public function buy_page_view($product_id) {

    $findProduct = Products::where([
      ['product_id', '=', $product_id]
    ])->exists();
    if ($findProduct) {
      $data = Products::getProductDetails($product_id);
      return view('buy-item')->with([
        'product' => $data,
        'product_id' => $product_id,
        'request' => request()
      ]);
    } else {
      abort(404);
    }

  }

  public function place_order(Request $request, Payments $order) {

    $coupon = $request->input('coupon_name');

    if (!empty($request->input('coupon_name'))) {

      if (Coupons::where('coupon_name', $coupon)->exists()) {

        $couponData = Coupons::getCouponData($coupon);

        $getProductData = Products::find($request->input('product_id'));

        $productRealPrice = ($getProductData->vat_value + $getProductData->delivery_value + $getProductData->product_price);

        $isOrderExists = Payments::where([
          ['process_product_id', '=', $request->input('product_id')],
          ['process_for_user', '=', auth()->id()]
        ])->count();

        if ($isOrderExists >= 1) {
          return 'order_exists';
        } else {
          if ($productRealPrice === intval($request->input('total_price'))) {
            $order->create([
              'applied_coupon' => $couponData[0]->coupon_id,
              'process_product_id' => $request->input('product_id'),
              'process_for_user' => auth()->id(),
              'process_delivery_man' => 1,
              'agent_warranty' => 1,
              'process_quantity' => $request->input('quantity'),
              'process_product_color' => $request->input('colors'),
              'total_price' => ($productRealPrice - $couponData[0]->coupon_value),
              'delivered_in' => now(),
            ]);
            Coupons::where('coupon_id', $couponData[0]->coupon_id)->update([
              'usable' => $couponData[0]->usable - 1
            ]);
            return 'order_is_done';
          } else {
            return 'price_is_not_okay';
          }
        }

      } else {

        return 'no_coupon_exists';

      }

    } else {

      $getProductData = Products::find($request->input('product_id'));

      $productRealPrice = ($getProductData->vat_value + $getProductData->delivery_value + $getProductData->product_price);

      $isOrderExists = Payments::where([
        ['process_product_id', '=', $request->input('product_id')],
        ['process_for_user', '=', auth()->id()]
      ])->count();

      if ($isOrderExists >= 1) {
        return 'order_exists';
      } else {
        $order->create([
          'applied_coupon' => 1,
          'process_product_id' => $request->input('product_id'),
          'process_for_user' => auth()->id(),
          'process_delivery_man' => 1,
          'agent_warranty' => 1,
          'process_quantity' => $request->input('quantity'),
          'process_product_color' => $request->input('colors'),
          'total_price' => $productRealPrice,
          'delivered_in' => now(),
        ]);
        return 'order_is_done';
      }

    }

  }

}
