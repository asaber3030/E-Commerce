<?php

namespace App\Http\Controllers;

use App\Models\Payments;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class UserController extends Controller {
  public function __construct() {
    $this->middleware('auth');
  }
  public function view_order_blade($process_id) {
    $payment = Payments::getPaymentID($process_id);
    if (!Payments::where('process_id', $process_id)->exists()) {
      return redirect()->route('account-orders');
    } else {
      return view('auth.view-order')->with([
        'id' => $process_id,
        'payment' => $payment
      ]);
    }
  }

  public function index() {
    return view('auth.profile');
  }

  public function orders_view() {
    return view('auth.user-orders');
  }

  public function update_view() {
    return view('auth.update');
  }

  public function addresses_view() {
    return view('auth.addresses.user-address');
  }
  public function update_addresses_view() {
    return view('auth.addresses.update-address');
  }
  public function delete_addresses_view() {
    return view('auth.addresses.delete-address');
  }

  public function points_view() {
    return view('auth.user-points');
  }

  public function update_password_view()
  {
    return view('auth.change-password');
  }

  public function payments_view() {
    return view('auth.payments');
  }

  public function payments_actions(Request $request) {
    Payments::where('process_id', $request->input('process_id'))->delete();
    $request->session()->flash('deleted', 'Your order has been canceled');
    return redirect()->route('account-payments');
  }

  public function refund_view() {
    return view('auth.refund');
  }
  public function refund_requests_view() {
    return view('auth.refund-request');
  }

  public function warranty_view() {
    return view('auth.warranty');
  }

  public function update_account_action(Request $request) {

    $user = Auth::user();

    if ($user->username != $request->input('username') || $user->email != $request->input('email') || $user->firstname != $request->input('firstname') || $user->lastname != $request->input('lastname') || $user->phone_number != $request->input('phone_number') || $user->town != $request->input('town'))
    {
      $validate = $this->validate($request, [
        'username'         => 'required|unique:users,username,' . $user->user_id . ',user_id|max:255|min:4',
        'email'         => 'required|unique:users,email,' . $user->user_id . ',user_id|max:255|min:4',
        'firstname'     => 'required|max:255|min:4',
        'lastname'      => 'required|max:255|min:4',
        'phone_number'         => 'required|unique:users,phone_number,' . $user->user_id . ',user_id|max:255|min:4',
        'town'          => 'required|max:255',
      ]);



      User::where('user_id', Auth::user()->user_id)->update([
        'username'        => $request->input('username'),
        'email'           => $request->input('email'),
        'firstname'       => $request->input('firstname'),
        'lastname'        => $request->input('lastname'),
        'phone_number'    => $request->input('phone_number'),
        'town'            => $request->input('town'),
        'updated_at'      => DB::raw('NOW()')
      ]);
      $request->session()->flash('success_msg', 'User updated successfully!');
      return redirect()->route('user-profile')->with(['flash_msg' => 'Updated_successfully']);
    } else {
      $request->session()->flash('failed_no_change', "Please make sure that you have typed new data to update information");
      return view('auth.update');
    }



  }

}
