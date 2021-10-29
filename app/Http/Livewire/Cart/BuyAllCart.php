<?php

namespace App\Http\Livewire\Cart;

use App\Models\Coupons;
use App\Models\Payments;
use App\Models\Products;
use App\Models\ShoppingCart;
use Livewire\Component;

class BuyAllCart extends Component
{

  public $couponIsGood;
  public $coupon_name;
  public $applyCoupon = false;
  public $couponValue;
  public $message;
  public $cart_id;
  public $total_price_no_coupon;
  public $total_price_coupon;

  public function __construct($id = null) {
    parent::__construct($id);
    $this->total_price_no_coupon = ShoppingCart::where([['cart_for_user', \Auth::id()] , ['completed', 0]])->sum('cart_price');
    $this->total_price_coupon = ShoppingCart::where([['cart_for_user', \Auth::id()] , ['completed', 0]])->sum('cart_price');
  }

  public function buyAll() {

    $getAllCarts = ShoppingCart::getProductsOfCurrentUser();

    // Payment with coupon

    if (!empty($this->coupon_name) && $this->findCoupon() === true) {

      $this->applyCoupon = true;

      $couponData = Coupons::where('coupon_name', $this->coupon_name)->get();

      $this->couponValue = $couponData[0]->coupon_value;

      $this->total_price_coupon = $this->total_price_coupon  - $couponData[0]->coupon_value;

      foreach ($getAllCarts as $cart) {

        $this->cart_id = $cart->cart_id;

        if (Payments::where([['process_product_id', $cart->product_id], ['process_for_user', \Auth::id()]])->exists()) {

          $this->message = "Cannot order this cart again!";

        } else {

          $productPieces = Products::select('pieces')->where('product_id', $cart->product_id)->get();

          Products::where('product_id', $cart->product_id)->update([
            'pieces' => $productPieces - $cart->cart_quantity
          ]);

          Payments::create([
            'applied_coupon' => $couponData[0]->coupon_id,
            'process_product_id' => $cart->product_id,
            'process_delivery_man' => 3,
            'process_for_user' => \Auth::id(),
            'agent_warranty' => 1,
            'process_quantity' => $cart->cart_quantity,
            'process_product_color' => $cart->cart_color,
            'process_cart_id' => $cart->cart_id,
            'total_price' => (($cart->product_price + $cart->vat_value + $cart->delivery_value) * $cart->cart_quantity) - $couponData[0]->coupon_value,
            'with_cart' => 1,
          ]);

          $this->message = "All Products has been ordered successfully!";

          session()->flash('ordered_success', 'All products has been ordered successfully. You can cancel them from here for 1 day before charging it to you.');

          ShoppingCart::where('cart_id', $cart->cart_id)->update(['completed' => 1]);

          redirect()->route('account-payments');

        }

      }

    } else {

      $this->message = "This coupon doesn't exist.";

    }

    // Without Coupon

    if (empty($this->coupon_name)) {

      foreach ($getAllCarts as $cart) {

        $this->cart_id = $cart->cart_id;

        if (Payments::where([['process_product_id', $cart->product_id], ['process_for_user', \Auth::id()]])->exists()) {

          $this->message = "Cannot order this cart again!";

        } else {

          Payments::create([
            'applied_coupon'   => 1,
            'process_product_id' => $cart->product_id,
            'process_delivery_man' => 3,
            'process_for_user' => \Auth::id(),
            'agent_warranty' => 1,
            'process_quantity' => $cart->cart_quantity,
            'process_product_color' => $cart->cart_color,
            'process_cart_id' => $cart->cart_id,
            'total_price' => ($cart->product_price + $cart->vat_value + $cart->delivery_value) * $cart->cart_quantity,
            'with_cart' => 1,
          ]);

          $this->message = "All Products has been ordered successfully!";

          session()->flash('ordered_success', 'All products has been ordered successfully. You can cancel them from here for 1 day before charging it to you.');

          ShoppingCart::where('cart_id', $cart->cart_id)->update(['completed' => 1]);

          redirect()->route('account-payments');

        }

      }

    }

  }

  public function findCoupon() {
    if (Coupons::where('coupon_name', $this->coupon_name)->exists() && !empty($this->coupon_name)) {
      return true;
    } else {
      $this->message = 'Coupon doesn\'t exists!';
      return false;
    }
  }

  public function render() {
    return view('livewire.cart.buy-all-cart');
  }
}
