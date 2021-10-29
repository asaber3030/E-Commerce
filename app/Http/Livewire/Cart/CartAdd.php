<?php

namespace App\Http\Livewire\Cart;

use App\Models\ShoppingCart;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CartAdd extends Component
{
  public $product_id;
  public $product_price;
  public $btnText = '<i class="fas fa-shopping-cart"></i> Add to Cart';

  public function mount($product_id, $product_price) {
    $this->product_id = $product_id;
    $this->product_price = $product_price;
  }

  public function addToCart() {
    if (!is_null(Auth::user())) {
      ShoppingCart::create([
        'cart_for_product' => $this->product_id,
        'cart_for_user' => Auth::id(),
        'cart_price' => $this->product_price
      ]);
      request()->session()->flash('message', 'Added..');
    }
  }

  public function render() {
    return view('livewire.cart.cart-add');
  }
}
