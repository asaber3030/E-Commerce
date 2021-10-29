<?php

namespace App\Http\Livewire\Orders;

use App\Models\Payments;
use Livewire\Component;

class ViewOrder extends Component {
  public $process_id;
  public $payment;
  public $user;

  public function __construct($id = null) {
    parent::__construct($id);
    $this->user = \Auth::user();
  }

  public function deleteSelected($id) {
    Payments::where('process_id', $id)->delete();
    session()->flash('notification', 'Your order has been canceled successfully!');
    return redirect()->route('account-orders');
  }

  public function mount($process_id, $payment) {
    $this->process_id = $process_id;
    $this->payment = $payment;
  }
  public function render() {
    return view('livewire.orders.view-order');
  }
}
