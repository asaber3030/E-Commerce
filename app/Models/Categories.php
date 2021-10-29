<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Categories extends Model
{

  use HasFactory;

  protected $table = 'categories';
  protected $primaryKey = 'category_id';

  protected $fillable = ['category_name', 'category_info', 'icon', 'category_keywords'];

  public static function getAllCategories($limit = 12) {
    return self::take($limit)->get();
  }

  public static function categories() {
    return self::where('category_deleted', '!=', 1)->get();
  }

  public static function getCategoryProducts($category_id): \Illuminate\Support\Collection {
    return DB::table('products')
      ->join('categories', 'products.product_category', 'categories.category_id')
      ->where('categories.category_id', $category_id)
      ->get();
  }

  public static function getSubCategories($category_id): \Illuminate\Support\Collection {
    return DB::table('sub_categories')
      ->where('belongs_to_category', $category_id)->get();
  }

  public static function getCategoryName($id) {
    return self::select('categories.category_name')
      ->where([
        ['category_id', '=', $id],
      ])->get()[0]->category_name;
  }


  // Admin

  public static function getDeletedCategories(int $limit = 10) {
    return self::where('category_deleted', 1)->paginate($limit);
  }

  public static function showCategories(int $limit = 10) {
    return self::where('category_deleted', '!=', 1)->paginate($limit);
  }

  public static function getAllDeletedCategories() {
    return self::where('category_deleted', 1)->get();
  }

  public static function subCats($cat_id) {
    return SubCategories::where('belongs_to_category', $cat_id)->get();
  }

  public static function getAllData($category_id) {
    return DB::table('sub_categories')
      ->select('sub_categories.sub_category_name')
      ->join('categories', 'sub_categories.belongs_to_category', 'categories.category_id')
      ->where('sub_categories.belongs_to_category', $category_id)
      ->get()->toArray();
  }

}
