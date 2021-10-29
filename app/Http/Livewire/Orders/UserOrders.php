<?php

namespace App\Http\Livewire\Orders;

use App\Models\Addresses;
use App\Models\Payments;
use Livewire\Component;

class UserOrders extends Component {
  public $allUserPayments;
  public $count;
  public $user;
  public $mainAddress;

  public function __construct($id = null) {
    $this->allUserPayments = Payments::currentUserPayments()->toArray();
    $this->count = count($this->allUserPayments);
    $this->user = \Auth::user();
    $this->mainAddress = Addresses::where([
      ['user_address', $this->user->user_id],
      ['is_main', 1]
    ])->get()->first();
    parent::__construct($id);
  }

  public function render() {
    return view('livewire.orders.user-orders');
  }

  public function deleteSelected($id) {
    Payments::where('process_id', $id)->delete();
    session()->flash('notification', 'Your order has been canceled successfully!');
    return redirect()->route('account-orders');
  }

}
