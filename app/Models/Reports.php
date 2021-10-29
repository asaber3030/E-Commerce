<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Reports extends Model {

  use HasFactory;

  protected $table = 'reports';
  protected $primaryKey = 'report_id';
  protected $fillable = ['report_for_user', 'report_message'];

  public static function allReports($paginate = 4) {
    return DB::table('reports')->join('users', 'reports.report_from_user', 'users.user_id')->paginate($paginate);
  }

  public static function latestReports($limit = 5) {
    return DB::table('reports')->join('users', 'reports.report_from_user', 'users.user_id')->orderBy('report_sent_at', 'DESC')->take($limit)->get();
  }

}
