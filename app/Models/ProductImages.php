<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImages extends Model
{
  use HasFactory;

  protected $table = 'product_images';
  protected $primaryKey = 'image_id';
  protected $fillable = [
    'image_path',
    'product_image_id',
    'main_image'
  ];


  public static function getMainImgOfProduct($id) {
    return self::where([
      ['product_image_id', '=', $id],
      ['main_image', '=', 1]
    ])->get()->first();
  }

  public static function getAllImages($id) {
    return self::where('product_image_id', '=', $id)->orderBy('main_image', 'DESC')->get()->toArray();
  }

  public static function showImages($id) {
    return self::where('product_image_id', '=', $id)->paginate(10);

  }

  public static function countImages($id) {
    return self::where('product_image_id', '=', $id)->count();
  }

  public function setFilenamesAttribute($value) {
    $this->attributes['image_path'] = json_encode($value);
  }

}
