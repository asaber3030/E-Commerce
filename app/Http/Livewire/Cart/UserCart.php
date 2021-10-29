<?php

namespace App\Http\Livewire\Cart;

use App\Models\Products;
use App\Models\ShoppingCart;
use Livewire\Component;

class UserCart extends Component {

  public $userCarts;
  public $countCarts;
  public $cartProducts;

  public $product_quantity;
  public $product_price;
  public $product_name;
  public $total_price;
  public $first_quantity;
  public $product_model;
  public $cart_id;

  public $product_id;
  public $product;

  public function __construct($id = null) {
    parent::__construct($id);
    $this->userCarts    = ShoppingCart::where('cart_for_user', \Auth::id())->get();
    $this->countCarts   = count($this->userCarts);
    $this->cartProducts = ShoppingCart::getProductsOfCurrentUser();
    $this->product      = Products::where('product_id', $this->product_id)->get();
    $this->total_price  = $this->product_price;
  }

  public function mount($product_id, $product_quantity, $product_name, $product_price, $total_price, $product_model, $cart_id)
  {
    $this->product_id = $product_id;
    $this->product_quantity = $product_quantity;
    $this->product_name = $product_name;
    $this->product_price = $product_price;
    $this->total_price = $total_price;
    $this->first_quantity = ShoppingCart::where('cart_for_product', $this->product_id)->get()[0]->cart_quantity;
    $this->product_model = $product_model;
    $this->cart_id = $cart_id;
  }

  public function increaseQuantity(): int {
    if ($this->first_quantity < $this->product_quantity) {
      ShoppingCart::where([
        ['cart_for_product', $this->product_id],
        ['cart_for_user', \Auth::id()],
      ])->update([
        'cart_quantity' => $this->first_quantity + 1,
      ]);
      return $this->first_quantity++;
    } else {
      return $this->first_quantity;
    }
  }

  public function decreaseQuantity(): int {
    if ($this->first_quantity !== 1) {
      ShoppingCart::where([
        ['cart_for_product', $this->product_id],
        ['cart_for_user', \Auth::id()],
      ])->update([
        'cart_quantity' => $this->first_quantity - 1,
      ]);
      return $this->first_quantity--;
    } else {
      return $this->first_quantity;
    }
  }

  public function render() {
    return view('livewire.cart.user-cart');
  }
}
