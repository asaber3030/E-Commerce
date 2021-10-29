<?php

namespace App\Http\Livewire;

use App\Models\Categories;
use App\Models\Products;
use Livewire\Component;
use App\Models\SubCategories;

class CategoriesDropdown extends Component {

  public $sub_categories;

  public $sub_category;
  public $sub_category_name;
  public $category;
  public $products;

  public $name;

  public function mount($sub_categories) {
    $this->sub_categories = $sub_categories;
    $this->sub_category = $this->sub_categories->first();
    $this->category = Categories::where('category_id', $this->sub_category->belongs_to_category)->get()->first();
    $this->products = Products::where('product_category', $this->category->category_id)
      ->whereBetween('product_price', [400, 700])
      ->limit(8)
      ->get();
  }

  public function changeView() {
    $this->name = 'hhl';
  }

  public function render() {
    return view('livewire.categories-dropdown');
  }
}
