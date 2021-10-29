<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DeliveryMen extends Model
{
  use HasFactory;
  protected $primaryKey = 'delivery_id';
  protected $table = 'delivery_men';
  protected $fillable = ['delivery_username', 'delivery_firstname', 'delivery_lastname', 'delivery_email', 'delivery_password', 'delivery_main_address', 'delivery_phone_number'];
  public $timestamps = true;

  // Delivery man
  public static function getDeliveredOrders()
  {
    return DB::table('payments')
      ->select('payments.delivered_in', 'payments.process_id', 'payments.inserted_in', 'payments.payment_type',
                'payments.process_product_color', 'payments.process_quantity',
                'payments.total_price',
                'products.product_name', 'products.product_price', 'products.currency', 'products.product_id',
                'products.vat_value', 'products.delivery_value', 'products.refund_value', 'users.town')
      ->join('delivery_men', 'payments.process_delivery_man', 'delivery_men.delivery_id')
      ->join('products', 'payments.process_product_id', 'products.product_id')
      ->join('users', 'payments.process_for_user', 'users.user_id')
      ->where([
        ['delivery_men.delivery_id', '=', request()->session()->get(sha1('delivery_id'))],
        ['payments.is_delivered', '=', 2]
      ])->get();
  }
  public static function undeliveredOrders()
  {
    return DB::table('payments')
      ->select('payments.delivered_in', 'payments.inserted_in', 'payments.payment_type',
        'payments.process_product_color', 'payments.process_quantity',
        'payments.total_price', 'payments.status', 'payments.process_id',
        'products.product_name', 'products.product_price', 'products.currency', 'products.product_will_arrive_in',
        'products.vat_value', 'products.delivery_value', 'products.refund_value', 'products.product_id', 'users.town')
      ->join('delivery_men', 'payments.process_delivery_man', 'delivery_men.delivery_id')
      ->join('products', 'payments.process_product_id', 'products.product_id')
      ->join('users', 'payments.process_for_user', 'users.user_id')
      ->where([
        ['delivery_men.delivery_id', '=', request()->session()->get(sha1('delivery_id'))],
        ['payments.is_delivered', '=', 1]
      ])->orderBy('payments.inserted_in')->orderBy('payments.status')
      ->get();
  }
  public static function countOrdersForMe() {
    return DB::table('payments')
      ->join('delivery_men', 'payments.process_delivery_man', 'delivery_men.delivery_id')
      ->where([
        ['delivery_men.delivery_id', '=', request()->session()->get(sha1('delivery_id'))],
        ['payments.is_delivered', '=', 1]
      ])->count();
  }
  public static function lastAssignedOrders($limit = 3)
  {
    return DB::table('payments')
      ->select('payments.delivered_in', 'payments.inserted_in', 'payments.payment_type',
        'payments.process_product_color', 'payments.process_quantity',
        'payments.total_price', 'payments.status', 'payments.process_id',
        'products.product_name', 'products.product_price', 'products.currency', 'products.product_will_arrive_in',
        'products.vat_value', 'products.delivery_value', 'products.refund_value', 'products.product_id', 'users.town')
      ->join('delivery_men', 'payments.process_delivery_man', 'delivery_men.delivery_id')
      ->join('products', 'payments.process_product_id', 'products.product_id')
      ->join('users', 'payments.process_for_user', 'users.user_id')
      ->where([
        ['delivery_men.delivery_id', '=', request()->session()->get(sha1('delivery_id'))],
        ['payments.is_delivered', '=', 1]
      ])->orderBy('payments.inserted_in', 'DESC')->orderBy('payments.status')->limit($limit)
      ->get();
  }
  public static function lastDelivered($limit = 3)
  {
    return DB::table('payments')
      ->select('payments.delivered_in', 'payments.process_id', 'payments.inserted_in', 'payments.payment_type',
        'payments.process_product_color', 'payments.process_quantity',
        'payments.total_price',
        'products.product_name', 'products.product_price', 'products.currency', 'products.product_id',
        'products.vat_value', 'products.delivery_value', 'products.refund_value', 'users.town')
      ->join('delivery_men', 'payments.process_delivery_man', 'delivery_men.delivery_id')
      ->join('products', 'payments.process_product_id', 'products.product_id')
      ->join('users', 'payments.process_for_user', 'users.user_id')
      ->where([
        ['delivery_men.delivery_id', '=', request()->session()->get(sha1('delivery_id'))],
        ['payments.is_delivered', '=', 2]
      ])->orderBy('payments.inserted_in', 'DESC')->orderBy('payments.status')->limit($limit)
        ->get();
  }

  // Admin
  public static function getAllDeliveryMen() {
    return DeliveryMen::select('*')->paginate(10);
  }
  public static function getAllDisabledDeliveryMen() {
    return DeliveryMen::where('status', 0)->paginate(10);
  }

  public static function sumDeliveryGain() {
    return DeliveryMen::sum('salary');
  }

  public static function disableDelivery($id) {
    return DeliveryMen::where('delivery_id', $id)->update([
      'status' => 0
    ]);
  }
  public static function allowDelivery($id) {
    return DeliveryMen::where('delivery_id', $id)->update([
      'status' => 1
    ]);
  }

  public static function disableAllDelivery(): bool {
    return self::where('status', 1)->update([
      'status' => 0
    ]);
  }
  public static function allowAllDelivery(): bool{
    return self::where('status', 0)->update([
      'status' => 1
    ]);
  }

}
