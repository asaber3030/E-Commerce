<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Favs extends Model
{
  use HasFactory;

  protected $table = 'favs';
  protected $primaryKey = 'fav_id';
  public $timestamps = false;
  protected $fillable = ['fav_product_id', 'fav_user_id', 'title'];

  public static function getUserFavs($id, $paginate = true, $paginateNums = 5) {
    if ($paginate == true) {

      return DB::table('favs')
        ->join('products', 'favs.fav_product_id', 'products.product_id')
        ->where('favs.fav_user_id', $id)
        ->paginate($paginateNums);

    } else {
      return DB::table('favs')
        ->join('products', 'favs.fav_product_id', 'products.product_id')
        ->where('favs.fav_user_id', $id)
        ->get();
    }
  }

}
