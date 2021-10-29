<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payments;

class PaymentsController extends Controller {

  public function paymentsIndex() {
    $payments = Payments::allPayments();
    return view('admin.payments.index')->with('payments', $payments);
  }
  public function paymentsActions(Request $request) {
    $payments = Payments::allPayments();

    if ($request->has('delete_process')) {
      Payments::where('process_id', $request->input('process_id'))->delete();
      $request->session()->flash('payments_msg', 'Process with ID: ' . $request->input('process_id') . ' Was Deleted Successfully');
      return redirect()->route('payments-index');
    }

    if ($request->has('delete_all')) {
      Payments::where('is_delivered', 2)->delete();
      $request->session()->flash('payments_msg', 'All Payments Was Deleted Successfully!');
      return redirect()->route('payments-index');
    }
    return view('admin.payments.index')->with('payments', $payments);

  }

  public function viewPaymentIndex() {
    return view('admin.payments.view');
  }

  public function undeliveredPaymentsIndex() {
    $uns = Payments::nonSelected();
    return view('admin.payments.undelivered')->with('payments', $uns);
  }
  public function undeliveredPaymentsActions(Request $request) {
    $payments = Payments::allPayments();

    if ($request->has('delete_process')) {
      Payments::where('process_id', $request->input('process_id'))->delete();
      $request->session()->flash('payments_msg', 'Process with ID: ' . $request->input('process_id') . ' Was Deleted Successfully');
      return redirect()->route('payments-index');
    }

    if ($request->has('delete_all')) {
      Payments::where('is_delivered', 1)->orWhere('is_delivered', 0)->delete();
      $request->session()->flash('payments_msg', 'All Undelivered Payments Was Deleted Successfully!');
      return redirect()->route('payments-undelivered-index');
    }
    return view('admin.payments.undelivered')->with('payments', $payments);
  }

}
