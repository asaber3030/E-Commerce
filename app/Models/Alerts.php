<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Alerts extends Model {

  use HasFactory;

  protected $table = 'alerts';
  protected $primaryKey = 'alert_id';
  protected $fillable = ['alert_title', 'alert_content', 'alert_type', 'alert_name', 'alert_for_admin'];

  public $timestamps = false;

  public static function allAlerts() {
    return self::all();
  }

  public static function latestAlerts($limit = 5): \Illuminate\Support\Collection {
    return DB::table('alerts')
      ->join('admins', 'alerts.alert_for_admin', 'admins.admin_id')
      ->where('alerts.alert_for_admin', Admin::admin()->admin_id)
      ->orderBy('alert_sent_in', 'DESC')
      ->take($limit)
      ->get();
  }

}
