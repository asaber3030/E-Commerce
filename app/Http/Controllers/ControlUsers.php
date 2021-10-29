<?php

namespace App\Http\Controllers;

use App\Models\Addresses;
use App\Models\Favs;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ControlUsers extends Controller {

  public function usersIndex() {
    $users = User::allAllowedUsers();
    return view('admin.users.index')->with('users', $users);
  }
  public function usersActions(Request $request) {
    if ($request->has('apply_disable')) {
      User::disableUser($request->input('user_id'));
      $request->session()->flash('users_msg', 'User has been disabled for now. Selected user cannot login');
    } elseif ($request->has('apply_allow')) {
      User::allowUser($request->input('user_id'));
      $request->session()->flash('users_msg', 'User has been allowed. Selected user can login again now');
    } elseif ($request->has('apply_allow_all')) {
      User::allowAllUsers();
      $request->session()->flash('users_msg', 'All Users has been allowed');
    } elseif ($request->has('apply_disable_all')) {
      User::disableAllUsers();
      $request->session()->flash('users_msg', 'All Users has been disabled');
    }

    return redirect()->route('users-index');
  }

  public function addUserIndex() {
    return view('admin.users.add');
  }
  public function addUserAction(Request $request) {
    $this->validate($request, [
      'username' => 'required|min:3|max:20|unique:users',
      'firstname' => 'required|min:3|max:20',
      'lastname' => 'required|min:3|max:20',
      'password' => 'required|min:8|max:20',
      'phone_number' => 'required|unique:users',
      'email' => 'required|min:8|max:100|unique:users',
      'town' => 'required|min:3|max:20',
      'address' => 'required|min:3|max:150'
    ]);


    User::create([
      'username' => $request->input('username'),
      'firstname' => $request->input('firstname'),
      'lastname' => $request->input('lastname'),
      'password' => Hash::make($request->input('password')),
      'phone_number' => $request->input('phone_number'),
      'email' => $request->input('email'),
      'town' => $request->input('town'),
      'main_address' => $request->input('address'),
    ]);

    $request->session()->flash('users_msg', 'User Has been Added Successfully');

    return redirect()->route('users-index');
  }

  public function viewUserIndex($user_id, Request $request) {
    $userInfo = User::where('user_id', $user_id)->get()->first();
    return view('admin.users.view')->with('user', $userInfo);
  }

  public function userPaymentsIndex($user_id, Request $request) {
    $userPayments = User::getAllPaymentsOfUser($user_id);
    $cUserPayments = User::countAllPayments($user_id);
    return view('admin.users.user-payments')->with(['payments' => $userPayments, 'count' => $cUserPayments]);
  }

  public function userFavsIndex($user_id, Request $request) {
    $userFavs = Favs::getUserFavs($user_id);
    $cUserFavs = count($userFavs);
    $userData = User::where('user_id', $user_id)->get()->first();
    return view('admin.users.user-favs')->with(['favs' => $userFavs, 'count' => $cUserFavs, 'user' => $userData]);
  }

  public function userAddressesIndex($user_id, Request $request) {
    $userAddresses = Addresses::getUserAddresses($user_id);
    $cUserAddresses = count($userAddresses);
    $userData = User::where('user_id', $user_id)->get()->first();
    return view('admin.users.user-addresses')->with(['addresses' => $userAddresses, 'count' => $cUserAddresses, 'user' => $userData]);
  }

  public function userRefundingIndex($user_id, Request $request) {
    $userProcess = User::getAllRefundingOfUser($user_id);
    $userData = User::where('user_id', $user_id)->get()->first();
    return view('admin.users.user-refunding')->with([
      'refunding' => $userProcess,
      'count' => count($userProcess),
      'user' => $userData
    ]);
  }

  public function userReportsIndex($user_id, Request $request) {
    $reports = User::getAllReportsOfUser($user_id);
    $userData = User::where('user_id', $user_id)->get()->first();

    return view('admin.users.user-reports')->with([
      'reports' => $reports,
      'count' => count($reports),
      'user' => $userData
    ]);
  }

  public function disabledUsersIndex() {
    return view('admin.users.disabled')->with('users', User::allDisabledUsers());
  }
  public function disabledUsersActions(Request $request) {
    if ($request->has('apply_allow')) {
      User::allowUser($request->input('user_id'));
      $request->session()->flash('users_msg', 'User has been allowed.');
    }

    return redirect()->route('disabled-users-index');

  }

}
