<?php

namespace App\Http\Livewire\Favourites;

use App\Models\Favs;
use Livewire\Component;

class Favourites extends Component {

  public $product_id;
  public $btnText = '<i class="fas fa-heart"></i> Wishlist';

  public function addToFavs() {
    Favs::create([
      'fav_product_id' => $this->product_id,
      'fav_user_id' => \Auth::id()
    ]);
  }

  public function mount($product_id) {
    $this->product_id = $product_id;
  }

  public function render() {
    return view('livewire.favourites.favourites');
  }
}
