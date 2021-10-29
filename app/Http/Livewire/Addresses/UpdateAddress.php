<?php

namespace App\Http\Livewire\Addresses;

use App\Models\Addresses;
use Livewire\Component;

class UpdateAddress extends Component
{

  public $old_address;
  public $old_google;
  public $address_id;
  public $address;
  public $is_main;

  public function __construct($id = null) {
    parent::__construct($id);
  }

  public function mount($address_id, $address) {
    $this->address_id = $address_id;
    $this->address = $address;
    $this->old_address = $this->address['address'];
    $this->old_google = $this->address['google_maps'];
    $this->is_main = $this->address['is_main'];
  }

  public function updateAddress() {

    $this->validate([
      'old_address' => 'required|different:address|min:5|max:255',
      'old_google' => 'required|url'
    ], ['old_address.different' => 'Change address title to update your address']);

    if ($this->is_main == true) {
      Addresses::where('user_address', \Auth::id())->update(['is_main' => 0]);
    }

    Addresses::where('address_id', $this->address_id)->update([
      'address' => $this->old_address,
      'google_maps' => $this->old_google,
      'is_main' => ($this->is_main == 1) ? 1 : 0
    ]);
    redirect()->route('account-addresses');
    session()->flash('notification', 'Selected address was updated successfully!');
  }


  public function render()
  {
    return view('livewire.addresses.update-address');
  }
}
