<?php

namespace App\Http\Controllers;

use App\Models\SubCategories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubCategoriesController extends Controller
{

  public function index($category_id, $sub_category_name, $sub_category_id) {
    $isExists =
      DB::table('sub_categories')
      ->join('categories', 'sub_categories.belongs_to_category', 'categories.category_id')
      ->where([
        ['categories.category_id', '=', $category_id],
        ['sub_categories.sub_category_id', '=', $sub_category_id],
        ['sub_categories.sub_category_name', '=', $sub_category_name]
        ])->exists();
    if ($isExists) {
      return view('sub-category')->with([
        'category_id' => $category_id,
        'sub_category_id' => $sub_category_id,
        'sub_category_name' => $sub_category_name
      ]);
    } else {
      return abort(404);
    }
  }

}
