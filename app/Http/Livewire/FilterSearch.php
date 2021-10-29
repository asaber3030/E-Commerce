<?php

namespace App\Http\Livewire;

use App\Models\Categories;
use App\Models\Products;
use Livewire\Component;

class FilterSearch extends Component {

  public $categories;
  public $brands;
  public $filter_cats = [];

  public $results = [];

  public function __construct($id = null) {
    parent::__construct($id);
    $this->categories = Categories::all();
    $this->brands = Products::select('brand')->distinct()->get()->toArray();

  }

  public function applyFilter() {
    foreach ($this->filter_cats as $cat => $value) {
      if (!in_array($value, $this->results)) {
        $getCategories = Categories::where('category_name', 'LIKE', '%' . $cat . '%')->get();
        foreach ($getCategories as $category) {
          $this->results[][$cat] = Products::where('product_category', $category->category_id)->get();
        }
      }
    }
  }

  public function render() {
    return view('livewire.filter-search');
  }
}
