<?php

namespace App\Http\Livewire\Favourites;

use App\Models\Favs;
use Livewire\Component;

class RemoveFavs extends Component
{

  public $product_id;
  public $btnText = '<i class="fas fa-heart-broken"></i> Remove from wishlist';

  public function removeFromFavs() {
    Favs::where([
      ['fav_product_id', $this->product_id],
      ['fav_user_id', \Auth::id()],
    ])->delete();
    redirect()->route('favourites');
  }

  public function mount($product_id) {
    $this->product_id = $product_id;
  }
  public function render()
  {
    return view('livewire.favourites.remove-favs');
  }
}
