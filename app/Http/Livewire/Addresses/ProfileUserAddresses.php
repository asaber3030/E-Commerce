<?php

namespace App\Http\Livewire\Addresses;

use App\Models\Addresses;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ProfileUserAddresses extends Component {

  public $userAddresses;
  public $countAddresses;

  public $new_address;
  public $new_google;
  public $msg;

  public $old_address;
  public $old_google;

  public $address_id = 1;

  public function __construct($id = null) {
    parent::__construct($id);
    $this->userAddresses  = Addresses::where('user_address', \Auth::id())->orderBy('is_main', 'DESC')->get();
    $this->countAddresses = count($this->userAddresses);
  }

  public function addAddress() {

    $this->validate([
      'new_address' => 'required|max:255|min:4',
      'new_google' => 'required|url'
    ]);

    if ($this->countAddresses < 5) {
      Addresses::create([
        'address' => $this->new_address,
        'google_maps' => $this->new_google,
        'is_main' => 0,
        'user_address' => \Auth::id(),
      ]);
      session()->flash('notification', 'Your address has been added successfully!');
    } else {
      session()->flash('notification', 'You have only 5 addresses to add. Can not add more than 5');
    }
    return redirect()->route('account-addresses');
  }

  public function deleteAllAddresses() {
    Addresses::where('user_address', \Auth::id())->delete();
    session()->flash('notification', 'All addresses has been deleted successfully!');
    return redirect()->route('account-addresses');
  }
  public function deleteSelected($address_id) {
    Addresses::where('address_id', $address_id)->delete();
    session()->flash('notification', 'Selected address has been deleted successfully');
    return redirect()->route('account-addresses');
  }

  public function render() {
    return view('livewire.addresses.profile-user-addresses');
  }
}
