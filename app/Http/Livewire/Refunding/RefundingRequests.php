<?php

namespace App\Http\Livewire\Refunding;

use Livewire\Component;
use App\Models\Refunding;
use App\Models\Products;

class RefundingRequests extends Component {

  public $user;
  public $requests;
  public $count;

  public function __construct($id = null) {
    parent::__construct($id);
    $this->user = \Auth::user();
    $this->requests = Refunding::getAllUserRefundRequests();
    $this->count = count($this->requests);
  }

  public function deleteSelected($id) {
    Refunding::where('refund_id', $id)->delete();
    session()->flash('notification', 'Selected refunding request has been deleted successfully!');
    return redirect()->route('account-refund');
  }

  public function render() {
    return view('livewire.refunding.refunding-requests');
  }
}
