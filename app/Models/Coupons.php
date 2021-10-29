<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Coupons extends Model
{
  use HasFactory;

  protected $table = 'coupons';
  protected $primaryKey = 'coupon_id';
  protected $fillable = ['coupon_name', 'coupon_value', 'usable', 'available'];

  public static function getCouponData($coupon) {
    return self::where('coupon_name', $coupon)->get();
  }
  public static function getAllDeletedCoupons() {
    return Coupons::where([
      ['deleted', '=', 1],
      ['coupon_id', '!=', 1]
    ])->paginate(10);
  }
  public static function getCouponDataWithID($coupon) {
    return self::where('coupon_id', $coupon)->get();
  }

  public static function isCouponExists($coupon) {
    return self::where([
      ['coupon_id', '=', $coupon],
      ['coupon_id', '!=', 1],
      ['deleted', '=', 0],
    ])->exists();
  }

  public static function getAllCoupons($limitPaginate = 10) {
    return Coupons::where([
      ['deleted', '=', 0],
      ['coupon_id', '!=', 1]
    ])->paginate(10);
  }
  public static function countAllCoupons(): int
  {
    return self::all()->count();
  }

  public static function disableAll()
  {
    return DB::table('coupons')->update([
      'available' => 0
    ]);
  }
  public static function disableSelected($id) {
    return self::where('coupon_id', $id)->update([
      'available' => 0
    ]);
  }

  public static function allowAll(): int {
    return DB::table('coupons')->update([
      'available' => 1
    ]);
  }
  public static function allowSelected($id) {
    return self::where('coupon_id', $id)->update([
      'available' => 1
    ]);
  }

  public static function countDisabled() {
    return self::where('available', 0)->count();
  }
  public static function countAllowed() {
    return self::where('available', 1)->count();
  }

  public static function deleteAll(): int {
    return DB::table('coupons')->update([
      'deleted' => 1,
      'available' => 0
    ]);
  }
  public static function deleteSelected($id) {
    return self::where('coupon_id', $id)->update([
      'deleted' => 1
    ]);
  }

  public static function restoreAll(): int {
    return DB::table('coupons')->update([
      'deleted' => 0,
      'available' => 1
    ]);
  }
  public static function restoreSelected($id) {
    return self::where('coupon_id', $id)->update([
      'deleted' => 0
    ]);
  }

  public static function countDeleted() {
    return self::where('deleted', 1)->count();
  }
  public static function countUnDeleted() {
    return self::where('deleted', 0)->count();
  }
}
