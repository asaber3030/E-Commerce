<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
  use HasFactory;

  protected $table = 'admins';
  protected $fillable = ['admin_firstname', 'admin_lastname', 'admin_username', 'admin_email', 'admin_password', 'admin_role', 'admin_phone', 'admin_picture', 'admin_address'];
  protected $primaryKey = 'admin_id';
  public $timestamps = false;

  public static function admin() {
    return self::find(request()->session()->get('admin_id') ?? 1)->get()->first();
  }

  // Admins route

  public static function getAllAdmins() {
    return self::where('admin_id', '!=', self::admin()->admin_id)->orderBy('admin_id')->paginate(10);
  }


  public static function disbaleAdmin($id) {
    return self::where('admin_id', $id)->update([
      'admin_role' => 0
    ]);
  }
  public static function allowAdmin($id) {
    return self::where('admin_id', $id)->update([
      'admin_role' => 1
    ]);
  }
  public static function superAdmin($id) {
    return self::where('admin_id', $id)->update([
      'admin_role' => 2
    ]);
  }
  public static function deleteAdmin($id) {
    return self::where('admin_id', $id)->delete();
  }

  public static function deleteAllAdmins() {
    return self::where('admin_id', '!=', self::admin()->admin_id)->delete();
  }
  public static function disableAllAdmins() {
    return self::where('admin_id', '!=', self::admin()->admin_id)->update([
      'admin_role' => 0
    ]);
  }
  public static function superAllAdmins() {
    return self::where('admin_id', '!=', self::admin()->admin_id)->update([
      'admin_role' => 2
    ]);
  }
  public static function allowAllAdmins() {
    return self::where('admin_id', '!=', self::admin()->admin_id)->update([
      'admin_role' => 1
    ]);
  }

}
