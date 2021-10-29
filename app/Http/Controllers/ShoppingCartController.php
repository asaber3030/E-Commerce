<?php

namespace App\Http\Controllers;

use App\Models\ShoppingCart;
use Gloudemans\Shoppingcart\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShoppingCartController extends Controller
{


  public function storeCart($product_id, $product_name, $quantity, $product_price) {
    Cart::add($product_id, $product_name, $quantity, $product_price)->associate('App\Models\ShoppingCart');
    request()->session()->flash('added_to_cart', 'Product was added successfully');
    return redirect()->route('shopping-cart');
  }

}
