<?php

namespace App\Http\Livewire\Refunding;

use App\Models\Payments;
use App\Models\Refunding;
use Livewire\Component;

class NewRefundRequest extends Component {

  public $products;

  public $phone_number;
  public $details;
  public $product_id;

  public function __construct($id = null) {
    parent::__construct($id);
    $this->products = Payments::currentUserPayments();
  }



  public function render() {
    return view('livewire.refunding.new-refund-request');
  }
}
