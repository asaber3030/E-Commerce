<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\SubCategories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoriesController extends Controller
{
  public function view_category($category_name, $id) {
    $findCategory = Categories::where([
      ['category_name', '=', $category_name],
      ['category_id', '=', $id],
    ])->exists();
    if ($findCategory) {

      return view('category')->with([
        'category_id' => $id,
        'category_name' => $category_name
      ]);
    } else {
      abort(404);
    }
  }

  // Admin
  public function categories_index() {
    return view('admin.categories.categories');
  }
  public function categories_actions(Request $request) {
    if ($request->has('delete-selected-category')) {
      Categories::where('category_id', $request->input('category_id'))->update([
        'category_deleted' => 1
      ]);
      $request->session()->flash('selected-category-deleted_msg', 'Selected category was deleted successfully');
      return redirect()->route('categories-index');
    }
    if ($request->has('delete_all_categories')) {
      DB::table('categories')->update([
        'category_deleted' => 1
      ]);
      $request->session()->flash('all_categories_deleted', "All Categories has been deleted successfully");
      return redirect()->route('deleted-categories-index');
    }
  }

  public function category_update_index($category_id, $category_name, Request $request) {
    $exists = Categories::where([
      ['category_id', '=', $category_id],
      ['category_name', '=', $category_name]
    ])->exists();

    if ($exists) {
      $get = Categories::where([
        ['category_id', '=', $category_id],
        ['category_name', '=', $category_name]
      ])->get();
      return view('admin.categories.update')->with('category', $get->first());
    } else {
      return redirect()->route('categories-index');
    }

  }
  public function category_update_action($category_id, $category_name, Request $request) {
    $this->validate($request, [
      'category_name' => 'required|min:3|max:50',
      'category_keywords' => 'required|min:10|max:255',
      'category_info' => 'required|min:50|max:500',
    ]);

    if ($request->hasFile('icon')) {
      $name = time() . "_" . rand() . '.' . $request->file('icon')->extension();
      $request->file('icon')->move(public_path('categories_icons'), $name);

      Categories::where('category_id', $category_id)->update([
        'category_name' => $request->input('category_name'),
        'category_keywords' => $request->input('category_keywords'),
        'category_info' => $request->input('category_info'),
        'icon' => url('categories_icons') . '/' . $name,
      ]);

    } else {
      Categories::where('category_id', $category_id)->update([
        'category_name' => $request->input('category_name'),
        'category_keywords' => $request->input('category_keywords'),
        'category_info' => $request->input('category_info'),
      ]);
    }

    $request->session()->flash('category_updated_msg', "Category $category_name was updated successfully");

    return redirect()->route('categories-index');
  }

  public function add_category_index() {
    return view('admin.categories.add-category');

  }
  public function add_category_action(Request $request) {
    $this->validate($request, [
      'category_name' => 'required|min:3|max:50|unique:categories',
      'category_keywords' => 'required|min:3|max:255|unique:categories',
      'category_info' => 'required|min:3|max:500|unique:categories',
      'icon' => 'required|max:4092',
    ]);

    $name = time() . "_" . rand() . '.' . $request->file('icon')->extension();
    $request->file('icon')->move(public_path('categories_icons'), $name);

    Categories::create([
      'category_name' => $request->input('category_name'),
      'category_keywords' => $request->input('category_keywords'),
      'category_info' => $request->input('category_info'),
      'icon' => url('categories_icons') . '/' . $name,
    ]);

    $request->session()->flash('category_added_msg', "Category was added successfully");
    return redirect()->route('categories-index');


  }

  public function deleted_categories_index() {
    return view('admin.categories.deleted-categories');
  }
  public function deleted_categories_actions(Request $request) {
    if ($request->has('apply_restore_all')) {
      DB::table('categories')->update([
        'category_deleted' => 0
      ]);
      $request->session()->flash('all_categories_restored', "All Categories has been restored successfully");
      return redirect()->route('categories-index');
    } elseif ($request->has('restore-selected-category')) {
      DB::table('categories')->where('category_id', $request->input('category_id'))->update([
        'category_deleted' => 0
      ]);
      $request->session()->flash('restore_selected_category', "Selected Category Restored successfully");
      return redirect()->route('categories-index');
    }

    return view('admin.categories.deleted-categories');
  }

  public function sub_categories_index($category_id) {
    if (Categories::where('category_id', $category_id)->exists()) {
      $sub_categories_data = SubCategories::getSubCategories($category_id);
      return view('admin.categories.sub-categories')->with([
        'sub' => $sub_categories_data,
        'category_id' => $category_id,
        'category' => Categories::where('category_id', $category_id)->get()->first()
      ]);
    } else {
      return redirect()->route('categories-index');
    }
  }
  public function sub_categories_actions($category_id, Request $r) {
    if ($r->has('delete-selected-sub')) {
      SubCategories::where([
        ['belongs_to_category', '=', $category_id],
        ['sub_category_id', '=', $r->input('sub_id')]
      ])->update([
        'sub_deleted' => 1
      ]);
      echo "Hello";
      $r->session()->flash('deleted_sub_msg', "Selected Sub Category Was Deleted successfully");
      return redirect()->route('sub-categories-index', [$category_id]);
    }

    if ($r->has('delete_all_sub')) {
      SubCategories::where([
        ['belongs_to_category', '=', $category_id],
      ])->update([
        'sub_deleted' => 1
      ]);
      $r->session()->flash('deleted_sub_all_msg', "All Sub Categories of this category were Deleted successfully");
      return redirect()->route('sub-categories-index', [$category_id]);
    }

  }

  public function update_sub_index($sub_id, $category_id) {
    if (SubCategories::where('sub_category_id', $sub_id)->exists()) {
      return view('admin.categories.update-sub')->with([
        'sub_id' => $sub_id,
        'category_id' => $category_id,
        'sub' => SubCategories::where('sub_category_id', $sub_id)->get()->first()
      ]);
    } else {
      return redirect()->route('categories-index');
    }
  }
  public function update_sub_action($sub_id, $category_id, Request $request) {
    $this->validate($request, [
      'sub_category_name' => 'required|min:3|max:100',
      'sub_category_keywords' => 'required|min:3|max:100',
    ]);
    if ($request->hasFile('sub_category_icon')) {
      $name = time() . "_" . rand() . '.' . $request->file('sub_category_icon')->extension();
      $request->file('sub_category_icon')->move(public_path('categories_icons'), $name);

      SubCategories::where([
        ['sub_category_id', '=', $sub_id],
        ['belongs_to_category', '=', $category_id],
      ])->update([
        'sub_category_name' => $request->input('sub_category_name'),
        'sub_category_keywords' => $request->input('sub_category_keywords'),
        'belongs_to_category' => intval($request->input('belongs_to_category')),
        'sub_category_icon' => url('categories_icons') . '/' . $name,
        'deleted_sub' => 0,
      ]);
      $request->session()->flash('updated_sub', "Selected Category Was Updated Successfully");
      return redirect()->route('sub-categories-index', [$category_id]);

    } else {
      SubCategories::where([
        ['sub_category_id', '=', $sub_id],
        ['belongs_to_category', '=', $category_id],
      ])->update([
        'sub_category_name' => $request->input('sub_category_name'),
        'sub_category_keywords' => $request->input('sub_category_keywords'),
        'belongs_to_category' => intval($request->input('belongs_to_category')),
      ]);
      $request->session()->flash('updated_sub', "Selected Category Was Updated Successfully");
      return redirect()->route('sub-categories-index', [$category_id]);
    }
  }

  public function add_sub_index($category_id) {
    if (Categories::where('category_id', $category_id)->exists()) {
      return view('admin.categories.add-sub')->with([
        'category_id' => $category_id,
        'category' => Categories::where('category_id', $category_id)->get()->first()
      ]);
    } else {
      return redirect()->route('sub-categories-index', [$category_id]);
    }
  }
  public function add_sub_action($category_id,  Request $request) {

    $this->validate($request, [
      'sub_category_name' => 'required|min:3|max:100|unique:sub_categories',
      'sub_category_keywords' => 'required|min:3|max:100',
      'sub_icon' => 'required',
    ]);


    $name = time() . "_" . rand() . '.' . $request->file('sub_icon')->extension();
    $request->file('sub_icon')->move(public_path('categories_icons'), $name);

    SubCategories::create([
      'sub_category_name' => $request->input('sub_category_name'),
      'sub_category_keywords' => $request->input('sub_category_keywords'),
      'belongs_to_category' => $request->input('category_id'),
      'sub_icon' => url('categories_icons') . '/' . $name,
    ]);

    $request->session()->flash('added_sub', "New Sub Category Was added successfully");
    return redirect()->route('sub-categories-index', [$category_id]);


  }

  public function sub_deleted_index($category_id) {
    if (Categories::where('category_id', $category_id)->exists()) {
      return view('admin.categories.deleted-sub')->with([
        'category_id' => $category_id,
        'category' => Categories::where('category_id', $category_id)->get()->first()
      ]);
    } else {
      return redirect()->route('sub-categories-index', [$category_id]);
    }

  }
  public function sub_deleted_action($category_id, Request $request) {

    if ($request->has('apply_restore_all')) {
      DB::table('sub_categories')->where('belongs_to_category', $category_id)->update([
        'sub_deleted' => 0
      ]);
      $request->session()->flash('all_sub_restored', "All Categories has been restored successfully");
      return redirect()->route('sub-categories-index', [$category_id]);

    } elseif ($request->has('restore-selected-sub')) {
      DB::table('sub_categories')->where([
        ['sub_category_id', '=', $request->input('sub_id')],
        ['belongs_to_category', '=', $category_id]

      ])->update(['sub_deleted' => 0]);
      $request->session()->flash('restore_selected_sub', "Selected Sub Category Restored successfully");
      return redirect()->route('sub-categories-index', [$category_id]);
    }

  }
}
