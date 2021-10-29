<?php

namespace App\Http\Controllers;

use App\Models\Favs;
use App\Models\ProductImages;
use App\Models\Products;
use App\Models\SubCategories;
use Illuminate\Http\Request;

class ProductsController extends Controller
{

  // User
  public function show_product_info($product_id) {
    $findProduct = Products::find($product_id)->exists();
    if ($findProduct) {
      $data = Products::getProductDetails($product_id);
      return view('view-item')->with([
          'product' => $data,
          'product_id' => $product_id
        ]);
    } else {
      abort(404);
    }
  }
  public function buy_view($product_id, Request $request) {
    Favs::create([
      'fav_product_id' => $request->input('product_id'),
      'fav_user_id' => $request->input('user_id'),
      'title' => 'hello world'
    ]);
  }

  // Admin
  public function productsView() {
    return view('admin.products.products');
  }
  public function productsActions(Request $request) {
    if ($request->has('delete-selected')) {
      Products::deleteSelected($request->input('product_id'));
      $request->session()->flash('delete_selected_product_msg', 'Product was deleted successfully. It can be restored from deleted products page');
      return redirect()->route('products-index');
    }

    if ($request->has('restore-selected')) {
      Products::restoreSelected($request->input('product_id'));
      $request->session()->flash('restore_selected_product_msg', 'Product was restored successfully.');
      return redirect()->route('products-deleted-index');
    }
    if ($request->has('apply_restore_all')) {
      Products::restoreAll();
      $request->session()->flash('restore_all_products_msg', 'All Products was restored successfully.');
      return redirect()->route('products-index');
    }
    if ($request->has('apply_delete_all')) {
      Products::deleteAll();
      $request->session()->flash('delete_all_products_msg', 'All Products was deleted successfully. All products can be restored in deleted ');
      return redirect()->route('products-index');
    }
    return redirect()->route('products-index');
  }

  public function deletedProductsView() {
    return view('admin.products.deleted');
  }
  public function viewProduct($product_id) {
    $exists = Products::where('product_id', $product_id);
    if ($exists) {
      $info = Products::getInfo($product_id);
      return view('admin.products.view')->with('product', $info);
    } else {
      return redirect('products-index');
    }
  }

  public function updateProductView($product_id) {
    $exists = Products::where('product_id', $product_id);
    if ($exists) {
      $info = Products::getInfo($product_id);
      return view('admin.products.edit')->with('product', $info);
    } else {
      return redirect('products-index');
    }
  }
  public function updateProductAction($product_id, Request $request) {

    if ($request->has('apply_edit_for_product')) {

      $validate = $this->validate($request, [
        'product_name' => 'required|min:5|max:255',
        'product_price' => 'required|min:1|max:11',
        'product_old_price' => 'required|min:1|max:11',
        'product_color' => 'required|min:2|max:255',
        'category' => 'required|numeric',
        'sub_category' => 'required|numeric',
        'model_name' => 'required|min:10|max:255',
        'brand' => 'required|min:3|max:255',
        'currency' => 'required|min:1|max:5',
        'vat_value' => 'required|numeric',
        'refund_value' => 'required|numeric|gt:5',
        'delivery_value' => 'required|numeric|gt:5',
        'pieces' => 'required|numeric',
        'stars' => 'required|numeric',
        'add_points' => 'required|numeric',
        'buy_by_points' => 'required|numeric',
        'product_info' => 'required|min:50|max:500',
      ]);

      Products::where('product_id', $product_id)->update([
        'product_name' => $request->input('product_name'),
        'product_price' => $request->input('product_price'),
        'product_old_price' => $request->input('product_old_price'),
        'product_color' => $request->input('product_color'),
        'product_category' => $request->input('category'),
        'product_sub_category' => $request->input('sub_category'),
        'model_name' => $request->input('model_name'),
        'brand' => $request->input('brand'),
        'currency' => $request->input('currency'),
        'vat_value' => $request->input('vat_value'),
        'refund_value' => $request->input('refund_value'),
        'delivery_value' => $request->input('delivery_value'),
        'pieces' => $request->input('pieces'),
        'stars' => $request->input('stars'),
        'add_points' => $request->input('add_points'),
        'buy_by_points' => $request->input('buy_by_points'),
        'product_info' => $request->input('product_info'),
      ]);

      $request->session()->flash('product_updated_msg_successfully', 'Product was updated successfully');
      return redirect()->route('products-index');
    }

    return SubCategories::where('belongs_to_category', $request->input('category_id'))->get();

  }

  public function addProductView() {
    return view('admin.products.add');
  }
  public function addProductAction(Request $request) {

    if ($request->has('apply_add_new_product')) {

      $this->validate($request, [
        'product_name' => 'required|min:5|max:255',
        'product_price' => 'required|min:1|max:11',
        'product_old_price' => 'required|min:1|max:11',
        'product_color' => 'required|min:2|max:255',
        'category' => 'required|numeric',
        'sub_category' => 'required|numeric',
        'model_name' => 'required|min:10|max:255',
        'brand' => 'required|min:3|max:255',
        'currency' => 'required|min:1|max:5',
        'vat_value' => 'required|numeric',
        'refund_value' => 'required|numeric|gt:5',
        'delivery_value' => 'required|numeric|gt:5',
        'pieces' => 'required|numeric',
        'stars' => 'required|numeric',
        'add_points' => 'required|numeric',
        'buy_by_points' => 'required|numeric',
        'product_info' => 'required|min:50|max:500',
        'warranty' => 'required|numeric',
        'agent' => 'required|numeric',
      ]);

      Products::create([
        'product_name' => $request->input('product_name'),
        'product_price' => $request->input('product_price'),
        'product_old_price' => $request->input('product_old_price'),
        'product_color' => $request->input('product_color'),
        'product_category' => $request->input('category'),
        'product_sub_category' => $request->input('sub_category'),
        'model_name' => $request->input('model_name'),
        'brand' => $request->input('brand'),
        'currency' => $request->input('currency'),
        'vat_value' => $request->input('vat_value'),
        'refund_value' => $request->input('refund_value'),
        'delivery_value' => $request->input('delivery_value'),
        'pieces' => $request->input('pieces'),
        'stars' => $request->input('stars'),
        'add_points' => $request->input('add_points'),
        'buy_by_points' => $request->input('buy_by_points'),
        'product_info' => $request->input('product_info'),
        'warranty' => $request->input('warranty'),
        'agent' => $request->input('agent'),
        'has_coupon' => $request->has('has_coupon') ?? 0
      ]);

      $request->session()->flash('product_added_msg_successfully', 'Product was added successfully');
      return redirect()->route('products-index');
    }
    return SubCategories::where('belongs_to_category', $request->input('category_id'))->get();

  }

  public function picturesAddView($product_id, $product_name, Request $request) {
    $findProduct = Products::where([
      ['product_id', '=', $product_id],
      ['product_name', '=', $product_name],
    ])->exists();
    if ($findProduct) {
      $get = Products::find($product_id)->get()->first();
      return view('admin.products.pictures-add')->with('product', $get);
    } else {
      return redirect()->route('product-pictures-add-index');
    }
  }

  public function picsView($product_id, $product_name) {

    $findProduct = Products::where([
      ['product_id', '=', $product_id],
      ['product_name', '=', $product_name],
    ])->exists();

    if ($findProduct) {
      $get = Products::where('product_id', $product_id)->get()->first();
      return view('admin.products.pictures')->with(['product' => $get, 'pr_id' => $product_id]);
    } else {
      return redirect()->route('products-index');
    }

  }

  public function picsAction($product_id, $product_name, Request $request) {
    $get = Products::where('product_id', $product_id)->get()->first();

    if ($request->has('delete-selected-picture')) {
      ProductImages::where('image_id', $request->input('image_id'))->delete();
      $request->session()->flash('deleted_picture_msg', 'Picture was deleted successfully');
    } elseif ($request->has('main-selected-picture')) {
      ProductImages::where('product_image_id', $product_id)->update([
        'main_image' => 0
      ]);

      ProductImages::where([
        ['image_id', '=', $request->input('image_id')],
        ['product_image_id', '=', $product_id]
      ])->update([
        'main_image' => 1
      ]);
      $request->session()->flash('make_main_picture', 'Selected Picture is main picture of selected product');

    }

    return view('admin.products.pictures')->with(['product' => $get, 'pr_id' => $product_id]);
  }

}
