<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Messages extends Model
{
  use HasFactory;
  protected $table = 'messages';
  protected $primaryKey = 'message_id';

  public static function getMessagesOfCurrentDeliveryMan() {

    return self::where('message_delivery_man',  request()->session()->get(sha1('delivery_id'))
    )->orderBy('message_sent_at')->get();

  }

  public static function getSenders()
  {
    return DB::table('admins')
      ->join('messages', 'admins.admin_id', 'messages.message_admin')
      ->orderBy('message_sent_at', 'DESC')
      ->where('messages.message_delivery_man', request()->session()->get(sha1('delivery_id')))
      ->get();
  }

}
