<?php

namespace App\Http\Controllers;

use App\Models\Coupons;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CouponsController extends Controller {
  public function couponsView() {
    return view('admin.coupons.coupons');
  }
  public function couponsActions(Request $request) {
    if ($request->has('apply_disable_all')) {
      Coupons::disableAll();
      $request->session()->flash('disable_all_success_flash', 'All Coupons is disabled for now.');
      return redirect()->route('coupons-index');
    }
    elseif ($request->has('apply_allow_all')) {
      Coupons::allowAll();
      $request->session()->flash('allow_all_success_flash', 'All Coupons is allowed for now. Every user can use it.');
      return redirect()->route('coupons-index');
    }
    elseif ($request->has('apply_delete_all')) {
      Coupons::deleteAll();
      $request->session()->flash('delete_all_success_flash', 'All Coupons is deleted');
      return redirect()->route('coupons-index');
    }
    elseif ($request->has('apply_restore_all')) {
      Coupons::restoreAll();
      $request->session()->flash('restore_all_success_flash', 'All Coupons is restored');
      return redirect()->route('coupons-index');
    }
    elseif ($request->has('disable-selected')) {
      Coupons::disableSelected($request->input('coupon_id'));
      $request->session()->flash('disable_selected_msg', 'Selected coupon was disabled.');
      return redirect()->route('coupons-index');
    }
    elseif ($request->has('allow-selected')) {
      Coupons::allowSelected($request->input('coupon_id'));
      $request->session()->flash('allow_selected_msg', 'Selected coupon was allowed.');
      return redirect()->route('coupons-index');
    }
    elseif ($request->has('delete-selected')) {
      Coupons::deleteSelected($request->input('coupon_id'));
      $request->session()->flash('delete_selected_msg', 'Selected coupon was deleted. It can be restored again from deleted coupons page.');
      return redirect()->route('coupons-index');
    }

    return view('admin.coupons.coupons');
  }

  public function couponsAddView() {
    return view('admin.coupons.add');
  }
  public function couponsAddAction(Request $request) {
    $validate = $this->validate($request, [
      'coupon_name' => 'required|max:5|unique:coupons|min:3',
      'coupon_value' => 'required|numeric|gt:10',
      'usable' => 'required|numeric',
    ], [
      'coupon_name.required' => 'Coupon name is required.',
      'coupon_name.max' => 'Coupon Name cannot be more than 5 letters',
      'coupon_name.min' => 'Coupon name cannot be less than 3 letters',
      'coupon_name.unique' => 'Coupon name is already exists',

      'coupon_value.required' => 'Coupon value is required to complete adding coupon',
      'coupon_value.numeric' => 'Coupon value must be a number',

      'usable.digits_between' => 'Greater',
      'usable.required' => 'Usable times is required',
      'usable.numeric' => 'Usable times must be a number',
    ]);


    $addCoupon = Coupons::create([
      'coupon_name' => $request->input('coupon_name'),
      'coupon_value' => intval($request->input('coupon_value')),
      'usable' => intval($request->input('usable')),
      'available' => (intval($request->has('available')) ?? 0),
    ]);

    $request->session()->flash('coupon_added_successfully', 'Typed Coupon Was Added Successfully. it can be used now!');
    return view('admin.coupons.add');
  }

  public function couponsUpdateView($coupon_id) {
    $findCoupon = Coupons::isCouponExists($coupon_id);
    if ($findCoupon) {
      return view('admin.coupons.edit')->with('coupon_data', Coupons::getCouponDataWithID($coupon_id)->first());
    } else {
      return redirect()->route('coupons-index');
    }
  }
  public function couponsUpdateAction($coupon_id, Request $request) {
    $findCoupon = Coupons::isCouponExists($coupon_id);
    if ($findCoupon) {
      $data = Coupons::getCouponData($coupon_id);
      $validate = $this->validate($request,
        [
          'coupon_name' => 'required|max:5|min:3',
          'coupon_value' => 'required|numeric|gt:10',
          'usable' => 'required|numeric',
        ],
        [
          'coupon_name.required' => 'Coupon name is required.',
          'coupon_name.max' => 'Coupon Name cannot be more than 5 letters',
          'coupon_name.min' => 'Coupon name cannot be less than 3 letters',
          'coupon_name.unique' => 'Coupon name is already exists',

          'coupon_value.required' => 'Coupon value is required to complete adding coupon',
          'coupon_value.numeric' => 'Coupon value must be a number',

          'usable.digits_between' => 'Greater',
          'usable.required' => 'Usable times is required',
          'usable.numeric' => 'Usable times must be a number',
        ]);

      $updateCoupon = DB::table('coupons')->where('coupon_id', $coupon_id)->update([
        'coupon_name' => $request->input('coupon_name'),
        'coupon_value' => intval($request->input('coupon_value')),
        'usable' => intval($request->input('usable')),
        'available' => (intval($request->has('available')) ?? 0),
      ]);


      $request->session()->flash('coupon_updated_successfully', 'Coupon Was Updated Successfully. it can be used now!');
      return redirect()->route('coupons-index');

    } else {
      return redirect()->route('coupons-index');
    }

  }

  public function deletedCouponsView() {
    return view('admin.coupons.deleted');
  }
  public function deletedCouponsAction(Request $request) {
    if ($request->has('restore-selected')) {
      Coupons::restoreSelected($request->input('coupon_id'));
      $request->session()->flash('restore_selected_success_flash', 'Selected Coupons is restored');
      return redirect()->route('coupons-deleted-index');
    }
    elseif ($request->has('apply_restore_all')) {
      Coupons::restoreAll();
      $request->session()->flash('restore_all_success_flash', 'All Coupons is restored');
      return redirect()->route('coupons-index');
    }
  }
}
