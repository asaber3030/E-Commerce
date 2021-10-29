<?php

namespace App\Http\Controllers;

use App\Models\ProductImages;
use App\Models\Products;
use Illuminate\Http\Request;

class ProductImagesController extends Controller
{
  public function addProductPictures($product_id, $product_name, Request $request) {
    $this->validate($request, [
      'image_file' => 'required',
      'image_file.*' => 'image|max:4096'
    ]);

    $files = [];
    if ($request->hasFile('image_file')) {
      foreach ($request->file('image_file') as $file) {
        $name = time() . "_" . rand() . $file->getClientOriginalName() . '.' . $file->extension();
        $file->move(public_path('files'), $name);
        $files[] = $name;
      }
    }


    foreach ($files as $filee) {
      ProductImages::create([
        'image_path' => url('/files') . '/' . $filee,
        'product_image_id' => $product_id,
      ]);
      $request->session()->flash('pictures_uploaded', 'Pictures for product with ID: ' . $product_name . ' has been uploaded successfully.');
    }
    return view('admin.products.pictures-add')->with('product', Products::where('product_id', $product_id)->get()->first());


  }
}
