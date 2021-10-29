<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Refunding extends Model
{
  use HasFactory;

  protected $table        = 'refunding_process';
  protected $primaryKey   = 'refund_id';
  public $timestamps      = true;

  protected $fillable = ['refund_product', 'refund_user', 'refund_will_arrive_in', 'refund_details', 'phone_number'];


  public static function getAllUserRefundRequests() {
    return self::join('products', 'refunding_process.refund_product', 'products.product_id')
      ->where('refund_user',  auth()->id())
      ->get();
  }

  public static function isThereAnyRefundRequests() {
    return self::where('refund_user',  auth()->id())->get()->count();
  }

  public static function getProductData() {
    $refundRequests = self::where('refund_user', auth()->id());
    return $refundRequests->refunding_process;
  }

  public function products() {
    return $this->hasOne('App\Models\Products');
  }

  public static function getUserRefundProducts() {
    return DB::table('refunding_process')
      ->join('products', 'refunding_process.refund_product', '=', 'products.product_id')
      ->where('refunding_process.refund_user', '=', auth()->user()->user_id)
      ->get();
  }

}
