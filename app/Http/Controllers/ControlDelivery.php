<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\DeliveryMen;
use Illuminate\Http\Request;

class ControlDelivery extends Controller
{

  public function deliveryMenIndex() {
    return view('admin.delivery_men.index')->with('delivery_men', DeliveryMen::getAllDeliveryMen());
  }
  public function deliveryMenActions(Request $request) {

    if ($request->has('apply_disable_all')) {
      DeliveryMen::disableAllDelivery();
      $request->session()->flash('delivery_msg', 'All Delivery Men has been disabled for now.');

    } elseif ($request->has('apply_allow_all')) {
      DeliveryMen::allowAllDelivery();
      $request->session()->flash('delivery_msg', 'All Delivery Men has been allowed.');

    } elseif ($request->has('apply_disable')) {
      DeliveryMen::disableDelivery($request->input('delivery_id'));
      $request->session()->flash('delivery_msg', 'Selected Delivery man has been disabled successfully');

    } elseif ($request->has('apply_allow')) {
      DeliveryMen::allowDelivery($request->input('delivery_id'));
      $request->session()->flash('delivery_msg', 'Selected Delivery man has been allowed successfully');
    }
    return redirect()->route('delivery-index');
  }

  public function addDeliveryIndex() {
    return view('admin.delivery_men.add');
  }
  public function addDeliveryAction(Request $request) {
    $this->validate($request, [
      'delivery_username' => 'required|min:3|max:20|unique:delivery_men',
      'delivery_firstname' => 'required|min:3|max:20',
      'delivery_lastname' => 'required|min:3|max:20',
      'delivery_phone_number' => 'required|integer|unique:delivery_men',
      'delivery_email' => 'required|max:100|email|unique:delivery_men',
      'delivery_address' => 'required|min:8|max:100',
      'delivery_password' => 'required|min:8|max:100',
    ]);

    DeliveryMen::create([
      'delivery_username' => $request->input('delivery_username'),
      'delivery_firstname' => $request->input('delivery_firstname'),
      'delivery_lastname' => $request->input('delivery_lastname'),
      'delivery_phone_number' => $request->input('delivery_phone_number'),
      'delivery_email' => $request->input('delivery_email'),
      'delivery_main_address' => $request->input('delivery_address'),
      'status' => ($request->has('status')) ? 0 : 1,
      'delivery_password' => $request->input('delivery_password'),
    ]);

    $request->session()->flash('delivery_msg', 'Delivery Man has been added successfully');

    return redirect()->route('add-delivery-index');
  }

  public function viewDeliveryIndex() {
    return view('admin.delivery_men.view');
  }

  public function updateDeliveryIndex($delivery_id) {
    if (DeliveryMen::where('delivery_id', $delivery_id)->exists()) {
      return view('admin.delivery_men.update')->with('delivery', DeliveryMen::where('delivery_id', $delivery_id)->get()->first());
    } else {
      return redirect()->route('delivery-index');
    }
  }
  public function updateDeliveryAction($delivery_id, Request $request) {
    if (DeliveryMen::where('delivery_id', $delivery_id)->exists()) {

      $this->validate($request, [
        'delivery_username' => 'required|min:3|max:20',
        'delivery_firstname' => 'required|min:3|max:20',
        'delivery_lastname' => 'required|min:3|max:20',
        'delivery_phone_number' => 'required|integer',
        'delivery_email' => 'required|min:8|max:100|email',
        'delivery_address' => 'required|min:8|max:100',
      ]);

      DeliveryMen::where('delivery_id', $delivery_id)->update([
        'delivery_username' => $request->input('delivery_username'),
        'delivery_firstname' => $request->input('delivery_firstname'),
        'delivery_lastname' => $request->input('delivery_lastname'),
        'delivery_phone_number' => $request->input('delivery_phone_number'),
        'delivery_email' => $request->input('delivery_email'),
        'delivery_main_address' => $request->input('delivery_address'),
        'status' => ($request->has('status')) ? 0 : 1
      ]);

      $request->session()->flash('delivery_msg', 'Delivery Man Has Been Updated Successfully');

      return redirect()->route('update-delivery-index', [$delivery_id]);

    } else {

      return redirect()->route('delivery-index');
    }

  }
}
