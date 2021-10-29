<?php

namespace App\Http\Livewire;

use App\Models\Categories;
use App\Models\Products;
use App\Models\SubCategories;
use Livewire\Component;

class SearchBar extends Component
{

  public $search;
  public $results;
  public $findInProducts;
  public $findInCategories;
  public $findInSub;

  public function __construct($id = null) {
    parent::__construct($id);
  }

  public function searchFor() {

    $this->findInProducts = Products::where('product_name', 'LIKE', '%' . $this->search . '%')->orWhere('brand', 'LIKE', '%' . $this->search . '%')->get()->toArray();
    $this->findInCategories = Categories::where('category_name', 'LIKE', '%' . $this->search . '%')->orWhere('category_keywords', 'LIKE', '%' . $this->search . '%')->get()->toArray();
    $this->findInSub = SubCategories::where('sub_category_name', 'LIKE', '%' . $this->search . '%')->orWhere('sub_category_keywords', 'LIKE', '%' . $this->search . '%')->get()->toArray();

    $this->results['products'] = $this->findInProducts;
    $this->results['categories'] = $this->findInCategories;
    $this->results['sub'] = $this->findInSub;

  }

  public function goToSearch() {
    return redirect()->route('search-page')->with([
      'data' => $this->results
    ]);
  }

  public function render() {
    return view('livewire.search-bar');
  }
}
