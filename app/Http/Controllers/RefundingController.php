<?php

namespace App\Http\Controllers;

use App\Models\Refunding;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RefundingController extends Controller
{
  public function refund_view() {
    return view('auth.refunding.requests');
  }

  public function refund_new_action(Request $request): \Illuminate\Http\RedirectResponse {

    $findRefundRequest = Refunding::where('refund_product', $request->input('refund_product'))->exists();

    if (!$findRefundRequest) {
      $this->validate($request, [
        'phone_number' => 'required|integer',
        'refund_product'      => 'required|integer|unique:refunding_process',
        'details'      => 'required|min:50|max:255'
      ]);

      Refunding::create([
        'refund_product'        => $request->input('refund_product'),
        'refund_user'           => auth()->id(),
        'refund_will_arrive_in' => \Carbon\Carbon::now()->addDays(4),
        'refund_details'        => $request->input('details'),
        'phone_number'          => $request->input('phone_number')
      ]);

      session()->flash('notification', 'Your Request was sent successfully! please wait for response');

    } else {
      session()->flash('notification', 'Your Request is already exists');
    }
    return redirect()->route('account-refund');

  }

  public function refund_requests_view() {
    return view('auth.refunding.new-request');
  }

}
