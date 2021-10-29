<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SubCategories extends Model
{
  use HasFactory;

  protected $table = 'sub_categories';
  protected $primaryKey = 'sub_category_id';
  protected $fillable = ['sub_category_name', 'sub_category_keywords', 'sub_category_icon', 'belongs_to_category', 'deleted_sub'];

  public static function getSubCategoryName($id) {
    return self::select('sub_category_name')
      ->where([
        ['sub_category_id', '=', $id],
      ])->get()[0]->sub_category_name;
  }

  public static function getSubCategories($category_id) {
    return self::join('categories', 'sub_categories.belongs_to_category', 'categories.category_id')
      ->where([
        ['categories.category_id', '=', $category_id],
        ['sub_categories.sub_deleted', '=', 0],
      ])->paginate(10);
  }

  public static function getSubCategoriesNoPaginate($category_id) {
    return self::join('categories', 'sub_categories.belongs_to_category', 'categories.category_id')
      ->where([
        ['categories.category_id', '=', $category_id],
        ['sub_categories.sub_deleted', '=', 0],
      ])->get();
  }

  public static function getLimitedSub($category_id, $limit = 6) {
    return self::join('categories', 'sub_categories.belongs_to_category', 'categories.category_id')
      ->where([
        ['categories.category_id', '=', $category_id],
        ['sub_categories.sub_deleted', '=', 0],
      ])->limit($limit)->get();
  }

  public static function getCategory($category_id, $sub_id) {
    return self::join('categories', 'sub_categories.belongs_to_category', 'categories.category_id')
      ->where([
        ['categories.category_id', '=', $category_id],
        ['sub_categories.sub_category_id', '=', $sub_id],
        ['sub_categories.sub_deleted', '=', 0],
      ])->get();
  }

  public static function getAllDeletedSubCategories($category_id) {
    return self::where([
      ['sub_deleted', '=', 1],
      ['belongs_to_category', '=', $category_id]
    ])->paginate(10);
  }

  public static function countSubCategories($category_id) {
    return self::join('categories', 'sub_categories.belongs_to_category', 'categories.category_id')
      ->where('categories.category_id', $category_id)
      ->count();
  }

}
