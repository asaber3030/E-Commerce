<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable implements MustVerifyEmail
{
  use HasFactory, Notifiable;

  protected $fillable = [
    'username',
    'firstname',
    'lastname',
    'phone_number',
    'town',
    'main_address',
    'email',
    'password',
  ];

  protected $primaryKey = 'user_id';



  public static function allAllowedUsers() {
    return self::select('*')->paginate(10);
  }
  public static function allDisabledUsers() {
    return self::where('user_status', 0)->paginate(10);
  }

  public static function allowUser($id) {
    return self::where('user_id', $id)->update(['user_status' => 1]);
  }
  public static function disableUser($id) {
    return self::where('user_id', $id)->update(['user_status' => 0]);
  }

  public static function allowAllUsers() {
    return self::where('user_status', 0)->update(['user_status' => 1]);
  }
  public static function disableAllUsers() {
    return self::where('user_status', 1)->update(['user_status' => 0]);
  }

  public static function getAllPaymentsOfUser($user_id) {
    return DB::table('payments')
      ->join('users', 'payments.process_for_user', 'users.user_id')
      ->join('products', 'payments.process_product_id', 'products.product_id')
      ->join('delivery_men', 'payments.process_delivery_man', 'delivery_men.delivery_id')
      ->join('coupons', 'payments.applied_coupon', 'coupons.coupon_id')
      ->join('warranty_agents', 'payments.agent_warranty', 'warranty_agents.agent_id')
      ->where('payments.process_for_user', $user_id)
      ->paginate(4);
  }

  public static function countAllPayments($user_id) {
    return DB::table('payments')
      ->where('process_for_user', $user_id)
      ->count();
  }

  public static function getAllRefundingOfUser($user_id, $pag = 4): \Illuminate\Contracts\Pagination\LengthAwarePaginator {
    return DB::table('refunding_process')
      ->join('users', 'refunding_process.refund_user', 'users.user_id')
      ->join('products', 'refunding_process.refund_product', 'products.product_id')
      ->where('users.user_id', $user_id)
      ->paginate($pag);
  }

  public static function getAllReportsOfUser($user_id, $pag = 4): \Illuminate\Contracts\Pagination\LengthAwarePaginator {
    return DB::table('reports')
      ->join('users', 'reports.report_from_user', 'users.user_id')
      ->where('users.user_id', $user_id)
      ->paginate($pag);
  }

}
