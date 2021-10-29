<?php

namespace App\Http\Livewire\Cart;

use App\Models\ShoppingCart;
use Livewire\Component;

class RemoveFromCart extends Component
{
  public $cart_id;
  public $message;

  public function mount($cart_id) {
    $this->cart_id = $cart_id;
  }

  public function removeApplied() {
    ShoppingCart::where('cart_id', $this->cart_id)->delete();
    session()->flash("message", "Product removed from cart.");
    redirect()->route('shopping-cart');
  }
  public function render()
  {
    return view('livewire.cart.remove-from-cart');
  }
}
