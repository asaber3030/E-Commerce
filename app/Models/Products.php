<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Products extends Model
{
  use HasFactory;

  protected $table        = 'products';
  protected $primaryKey   = 'product_id';

  protected $fillable = [
    'product_name',
    'product_price',
    'product_old_price',
    'product_color',
    'product_category',
    'product_sub_category',
    'model_name',
    'brand',
    'currency',
    'vat_value',
    'refund_value',
    'delivery_value',
    'pieces',
    'stars',
    'add_points',
    'buy_by_points',
    'product_info',
    'agent',
    'warranty',
    'has_coupon'
  ];

  public static function getProductDetails($id) {
    return
      self::join('categories', 'products.product_category', 'categories.category_id')
      ->join('sub_categories', 'products.product_sub_category', 'sub_categories.sub_category_id')
      ->join('warranty_agents', 'products.agent', 'warranty_agents.agent_id')
      ->where('products.product_id', '=', $id)
      ->get()[0];
  }
  public static function specialProducts($limit = 2, $stars = 3) {
    return self::where('stars', '>=', $stars)->orderBy('stars', 'DESC')->take($limit)->get();
  }
  public static function getFullCategoryName($id, $sub_id): string {
    return Categories::getCategoryName($id) . ' > ' . SubCategories::getSubCategoryName($sub_id);
  }

  public static function getAllProducts($cate_id, $sub_id) {
    return self::where([
      'product_category' => $cate_id,
      'product_sub_category' => $sub_id
    ])->get();
  }
  public static function selectWhat($select) {
    return self::select($select)->get()->toArray();
  }

  public static function getInfo($product_id) {
    return self::join('categories', 'products.product_category', 'categories.category_id')
      ->join('sub_categories', 'products.product_sub_category', 'sub_categories.sub_category_id')
      ->join('warranty_agents', 'products.agent', 'warranty_agents.agent_id')
      ->where('product_id', $product_id)
      ->orderBy('products.product_id')
      ->get()->first();
  }

  public static function getAllProductsData() {
    return self::join('categories', 'products.product_category', 'categories.category_id')
      ->join('sub_categories', 'products.product_sub_category', 'sub_categories.sub_category_id')
      ->join('warranty_agents', 'products.agent', 'warranty_agents.agent_id')
      ->where('deleted', 0)
      ->orderBy('products.product_id')
      ->paginate(10);
  }
  public static function getAllDeletedProductsData() {
    return self::join('categories', 'products.product_category', 'categories.category_id')
      ->join('sub_categories', 'products.product_sub_category', 'sub_categories.sub_category_id')
      ->join('warranty_agents', 'products.agent', 'warranty_agents.agent_id')
      ->where('deleted', 1)
      ->orderBy('products.product_id')
      ->paginate(10);
  }

  public static function deleteSelected($id) {
    return self::where('product_id', $id)->update([
      'deleted' => 1
    ]);
  }
  public static function deleteAll() {
    return DB::table('products')->update([
      'deleted' => 1
    ]);
  }

  public static function restoreAll() {
    return DB::table('products')->update([
      'deleted' => 0
    ]);
  }
  public static function restoreSelected($id) {
    return self::where('product_id', $id)->update([
      'deleted' => 0
    ]);
  }


}
