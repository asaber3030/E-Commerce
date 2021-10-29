<?php

namespace App\Http\Controllers;

use App\Models\DeliveryMen;
use App\Models\Payments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DeliveryMenController extends Controller {

  public function attributes(): array {
    return [
      'delivery-username' => 'email address',
    ];
  }

  public function index() {
    $delivery = DeliveryMen::where('delivery_id', request()->session()->get(sha1('delivery_id')))->get()->first();
    if (!request()->session()->has(sha1('delivery_id'))) {
      return redirect()->route('login-delivery');
    }
    return view('delivery-men.index')->with('delivery', $delivery);
  }

  public function login_view(Request $request) {
    if ($request->session()->has(sha1('delivery_id'))) {
      return redirect()->route('delivery-dashboard');
    } else {
      return view('delivery-men.login');
    }
  }
  public function login(Request $request) {

    $validationErrors = [];

    $validate = $this->validate($request, [
      'delivery-username' => 'required',
      'delivery-password' => 'required'
    ]);

    $findDeliveryMan = DeliveryMen::where([
      ['delivery_username', '=', $request->input('delivery-username')],
      ['delivery_password', '=', sha1($request->input('delivery-password'))]
    ])->orWhere([
      ['delivery_email', '=', $request->input('delivery-username')],
      ['delivery_password', '=', sha1($request->input('delivery-password'))]
    ])->exists();

    $findAccount = DeliveryMen::where([
      ['delivery_username', '=', $request->input('delivery-username')],
    ])->orWhere([
      ['delivery_email', '=', $request->input('delivery-username')],
    ])->exists();
    if ($findAccount == false || $findDeliveryMan == false) {
      $validationErrors[] = "There's no account with such information";
      return view('delivery-men.login');
    } else {
      $delivery = DeliveryMen::where([
        ['delivery_username', '=', $request->input('delivery-username')],
        ['delivery_password', '=', sha1($request->input('delivery-password'))]
      ])->orWhere([
        ['delivery_email', '=', $request->input('delivery-username')],
        ['delivery_password', '=', sha1($request->input('delivery-password'))]
      ])->get()->first();
      $request->session()->put(sha1('delivery_id'), $delivery->delivery_id);
      return redirect()->route('delivery-dashboard');
    }

  }

  public function messages_view() {
    return view('delivery-men.messages');
  }

  public function delivered_orders_view() {
    return view('delivery-men.delivered_orders');
  }
  public function payment_order_view($payment_id, $product_id) {
    $findPayment = Payments::where([
      ['process_delivery_man', '=', request()->session()->get(sha1('delivery_id'))],
      ['process_id', '=', $payment_id]
    ])->exists();
    if ($findPayment) {
      $payment = Payments::join('products', 'payments.process_product_id', 'products.product_id')
        ->join('users', 'payments.process_for_user', 'users.user_id')
      ->where([
        ['process_delivery_man', '=', request()->session()->get(sha1('delivery_id'))],
        ['process_id', '=', $payment_id],
        ['process_product_id', $product_id]
      ])->get()
        ->first();

      return view("delivery-men.view-delivered-order")->with('payment', $payment);

    } else {

      return abort(404);

    }
  }

  public function pending_orders_view() {
    return view('delivery-men.pending-orders');
  }
  public function pending_orders_action(Request $request) {
    $delivery = DeliveryMen::where('delivery_id', request()->session()->get(sha1('delivery_id')))->get()->first();

    if ($request->has('delivered_order')) {
      DeliveryMen::where('delivery_id', $delivery->delivery_id)
        ->update([
        'salary' => $delivery->salary + $request->input('delivery_value')
      ]);
      Payments::where('process_id', $request->input('process_id'))->update([
        'is_delivered' => 2,
      ]);
      return redirect()->route('pending-orders');
    }
  }

  public function edit_delivery_view()
  {
    return view('delivery-men.edit');
  }
  public function edit_delivery_action(Request $request)
  {
    $user = DeliveryMen::where('delivery_id', request()->session()->get(sha1('delivery_id')))->get()->first();

    if (
        $user->delivery_username      != $request->input('deusername') ||
        $user->delivery_email         != $request->input('deemail') ||
        $user->delivery_firstname     != $request->input('defirstname') ||
        $user->delivery_lastname      != $request->input('delastname') ||
        $user->delivery_phone_number  != $request->input('dephone_number') ||
        $user->delivery_main_address  != $request->input('demain_address')
      )
    {
      $validate = $this->validate($request, [
        'deusername'              => 'required|unique:delivery_men,delivery_username,' . $user->delivery_id . ',delivery_id|max:255|min:4',
        'deemail'                 => 'required|unique:delivery_men,delivery_email,' . $user->delivery_id . ',delivery_id|max:255|min:4',
        'defirstname'             => 'required|max:255|min:4',
        'delastname'              => 'required|max:255|min:4',
        'dephone_number'          => 'required|unique:delivery_men,delivery_phone_number,' . $user->delivery_id . ',delivery_id|max:11|min:10',
        'demain_address'          => 'required|max:255',
      ]);

      DeliveryMen::where('delivery_id', $user->delivery_id)->update([
        'delivery_username'                => $request->input('deusername'),
        'delivery_email'                   => $request->input('deemail'),
        'delivery_firstname'               => $request->input('defirstname'),
        'delivery_lastname'                => $request->input('delastname'),
        'delivery_phone_number'            => $request->input('dephone_number'),
        'delivery_main_address'            => $request->input('demain_address'),
        'updated_at'              => DB::raw('NOW()')
      ]);
      $request->session()->flash('desuccess_msg', 'User updated successfully!');
      return redirect()->route('delivery-dashboard');
    } else {
      $request->session()->flash('defailed_no_change', "Please make sure that you have typed new data to update information");
      return view('delivery-men.edit');
    }
  }

}
