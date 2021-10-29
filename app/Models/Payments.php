<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Payments extends Model
{
  use HasFactory;

  protected $fillable = [
    'applied_coupon',
    'process_product_id',
    'process_for_user',
    'process_delivery_man',
    'agent_warranty',
    'process_product_color',
    'process_quantity',
    'total_price',
    'delivered_in',
    'process_cart_id'
  ];

  protected $table          = 'payments';
  protected $primaryKey     = 'process_id';
  public $timestamps        = true;


  // User
  public static function currentUserPayments() {
    return DB::table('payments')
      ->join('products', 'payments.process_product_id', '=', 'products.product_id')
      ->join('users', 'payments.process_for_user', '=', 'users.user_id')
      ->join('coupons', 'payments.applied_coupon', '=', 'coupons.coupon_id')
      ->join('warranty_agents', 'payments.agent_warranty', '=', 'warranty_agents.agent_id')
      ->where('users.user_id', '=', auth()->id())
      ->orderBy('is_delivered', 'ASC')
      ->orderBy('inserted_in', 'DESC')
      ->get();
  }
  public static function countPaymentsOfMe() {
    return self::where('process_for_user', auth()->id())->count();
  }

  public static function getPaymentID($id) {
    return DB::table('payments')
      ->join('products', 'payments.process_product_id', '=', 'products.product_id')
      ->join('users', 'payments.process_for_user', '=', 'users.user_id')
      ->join('coupons', 'payments.applied_coupon', '=', 'coupons.coupon_id')
      ->join('warranty_agents', 'payments.agent_warranty', '=', 'warranty_agents.agent_id')
      ->where([
        ['users.user_id', auth()->id()],
        ['payments.process_id', $id]
      ])->get()->toArray();
  }

  // Admin
  public static function allPayments($delivered = 2): \Illuminate\Contracts\Pagination\LengthAwarePaginator
  {
    return DB::table('payments')
      ->join('products', 'payments.process_product_id', '=', 'products.product_id')
      ->join('users', 'payments.process_for_user', '=', 'users.user_id')
      ->join('coupons', 'payments.applied_coupon', '=', 'coupons.coupon_id')
      ->join('delivery_men', 'payments.process_delivery_man', '=', 'delivery_men.delivery_id')
      ->join('warranty_agents', 'payments.agent_warranty', '=', 'warranty_agents.agent_id')
      ->orderBy('inserted_in', 'DESC')
      ->where('is_delivered', $delivered)
      ->paginate(10);
  }

  public static function nonSelected($where = 1, $or = 0): \Illuminate\Contracts\Pagination\LengthAwarePaginator
  {
    return DB::table('payments')
      ->join('products', 'payments.process_product_id', '=', 'products.product_id')
      ->join('users', 'payments.process_for_user', '=', 'users.user_id')
      ->join('coupons', 'payments.applied_coupon', '=', 'coupons.coupon_id')
      ->join('delivery_men', 'payments.process_delivery_man', '=', 'delivery_men.delivery_id')
      ->join('warranty_agents', 'payments.agent_warranty', '=', 'warranty_agents.agent_id')
      ->orderBy('inserted_in', 'DESC')
      ->where('is_delivered', $where)
      ->orWhere('is_delivered', $or)
      ->paginate(10);
  }


  public static function countAllPayments(): int {
    return self::all()->count();
  }

  public static function countUndeliveredPayments(): int {
    return self::where('is_delivered', 0)->orWhere('is_delivered', 1)->count();
  }
  public static function countDeliveredPayments(): int {
    return self::where('is_delivered', 2)->count();
  }

  public static function getTotalGain() {
    return self::sum('total_price');
  }

  public static function countPaymentsOfUser($id) {
    return self::where('process_for_user', $id)->count();
  }

}
