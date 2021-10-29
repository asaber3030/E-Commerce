<?php

namespace App\Http\Controllers;

use App\Models\Addresses;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AddressesController extends Controller
{
  public function new_address_view()
  {
    return view('auth.addresses.new-address');
  }
  public function new_address_action(Request $request)
  {

    $validate = $this->validate($request, [
      'address' => 'required|max:255|min:4',
    ]);

    if ($request->has('is_main')) {
      (new Addresses)->where('is_main', 1)->update(['is_main' => 0]);
    }

    Addresses::create([
      'address' => $request->input('address'),
      'google_maps' => $request->input('google_maps'),
      'is_main' => $request->has('is_main'),
      'user_address' => Auth::user()->user_id,
    ]);

    $request->session()->flash('new_address_created', 'Your address has been added successfully!');

    return view('auth.addresses.user-address');
  }

  public function update_addresses_view($id) {
    $addresses      = Addresses::where([
      ['user_address', '=', auth()->id()],
      ['address_id', '=', $id]
    ])->get()->count();
    if ($addresses == 0) {
      return redirect()->route('account-addresses');
    } else {
      return view('auth.addresses.update-address')->with('id', $id);
    }
  }
  public function update_address_action(Request $request, $id) {

    $user = Auth::user();
    $addresses = Addresses::where([
      ['user_address', '=', $user->user_id],
      ['address_id', '!=', $id]
    ])->update(['is_main' => 0]);


    $validate = $this->validate($request, [
      'address'         => 'required|min:4|max:255',
    ]);

    Addresses::where([
      ['user_address', '=', $user->user_id],
      ['address_id', '=', $id]])->update([
      'address'               => $request->input('address'),
      'google_maps'           => $request->input('google_maps'),
      'is_main'               => $request->has('is_main'),
    ]);
    $request->session()->flash('success_msg', 'Address updated successfully!');
    return redirect()->route('account-addresses')->with(
      [
        'flash_msg' => 'Updated_successfully',
        'id'        => $id
      ]
    );

  }

  public function delete_address_view($id) {
    $addresses      = Addresses::where([
      ['user_address', '=', auth()->id()],
      ['address_id', '=', $id]
    ])->get()->count();
    if ($addresses == 0) {
      return redirect()->route('account-addresses');
    } else {
      return view('auth.addresses.delete-address')->with('id', $id);
    }
  }
  public function delete_address_action(Request $request, $id) {
    $addresses = Addresses::where([
      ['user_address', '=', auth()->id()],
      ['address_id', '=', $id]
    ])->delete();
    return redirect()->route('account-addresses');
  }

  public function delete_all_view() {
    return view('auth.addresses.delete-all');
  }
  public function delete_all_action(Request $request) {
    $addresses = Addresses::where([
      ['user_address', '=', auth()->id()],
    ])->delete();
    return redirect()->route('account-addresses');
  }



}

