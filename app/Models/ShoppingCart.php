<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShoppingCart extends Model
{
  protected $table = 'cart';
  protected $primaryKey = 'cart_id';
  protected $fillable = ['cart_for_product', 'cart_for_user', 'cart_price'];
  public $timestamps = false;

  use HasFactory;

  public static function getProductsOfCurrentUser() {
    return self::join('products', 'cart.cart_for_product', 'products.product_id')
      ->where([['cart.cart_for_user', \Auth::id()] , ['completed', 0]])
      ->get();
  }

}
