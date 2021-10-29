<?php

namespace App\Models;

use http\Env\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Addresses extends Model
{
  use HasFactory;

  protected $fillable = ['address', 'google_maps', 'is_main', 'user_address'];
  protected $table = 'user_addresses';
  protected $primaryKey = 'address_id';
  public $timestamps = false;

  public static function getUserAddresses($user_id) {
    return self::where('user_address', $user_id)->get();
  }


}
