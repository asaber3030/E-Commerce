<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Replies extends Model
{
  use HasFactory;

  protected $table = 'reports_replies';
  protected $primaryKey = 'reply_id';
  protected $fillable = ['reply_content', 'reply_for_report', 'reply_for_user'];

  public $timestamps = false;
}
