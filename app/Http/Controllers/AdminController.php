<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Coupons;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller {

  public function home() {
    return view('admin.home');
  }

  public function adminLoginView() {
    return view('admin.admin-login');
  }
  public function adminLoginAction(Request $request) {

    $validate = $this->validate($request, [
      'admin_username' => 'required',
      'admin_password' => 'required',
    ], [
      'admin_username.required' => 'Username Or E-mail is required',
      'admin_password.required' => 'Administrator Password is required'
    ]);

    $findAdmin = Admin::where([
      ['admin_username', '=', $request->input('admin_username')],
      ['admin_password', '=', sha1($request->input('admin_password'))],
    ])->orWhere([
      ['admin_email', '=', $request->input('admin_username')],
      ['admin_password', '=', sha1($request->input('admin_password'))]
    ])->exists();

    if ($findAdmin) {
      $data = Admin::where([
        ['admin_username', '=', $request->input('admin_username')],
        ['admin_password', '=', sha1($request->input('admin_password'))],
      ])->orWhere([
        ['admin_email', '=', $request->input('admin_username')],
        ['admin_password', '=', sha1($request->input('admin_password'))]
      ])->get()->first();
      $request->session()->flash('admin_is_authorized_msg', 'Welcome back Mr. ' . $data->admin_firstname . ' you will be redirected to your dashboard. Please wait!');
      $request->session()->forget('admin_is_not_authorized_msg');
      $request->session()->put('admin_id', $data->admin_id);
      return redirect()->route('admin-dashboard');
    } else {
      $request->session()->flash('admin_is_not_authorized_msg', 'These E-mail Or Username of admin is not exist');

    }

    return view('admin.admin-login');

  }

  public function profileView() {
    $admin = Admin::admin() ?? null;
    return view('admin.admin_panel.profile')->with('admin', $admin);
  }

  public function changePasswordView() {
    return view('admin.admin_panel.update-password');
  }
  public function changePasswordAction(Request $request) {
    $admin = Admin::admin();

    $validate = $this->validate($request, [
      'old_password' => 'required',
      'new_password' => 'required|max:15|min:8|different:old_password'
    ]);
    if ($admin->admin_password === sha1($request->input('old_password'))) {
      Admin::where('admin_id', $admin->admin_id)->update([
        'admin_password' => sha1($request->input('new_password'))
      ]);
      $request->session()->flash('updating_msg', 'Password updated successfully');

      return redirect()->route('admin-profile');
    } else {
      $request->session()->flash('updating_msg', 'Invalid Password');
      return redirect()->back();
    }

  }

  public function changePersonalView() {
    return view('admin.admin_panel.update-personal')->with('admin', Admin::admin());
  }
  public function changePersonalAction(Request $request) {
    $this->validate($request, [
      'new_firstname' => 'required|min:4|max:30',
      'new_lastname' => 'required|min:4|max:30',
      'new_username' => 'required|min:4|max:8',
    ]);

    Admin::where('admin_id', Admin::admin()->admin_id)->update([
      'admin_firstname' => $request->input('new_firstname'),
      'admin_lastname' => $request->input('new_lastname'),
      'admin_username' => $request->input('new_username'),
    ]);

    $request->session()->flash('updating_msg', 'Personal Information has been updated successfully');
    return redirect()->route('admin-profile');

  }

  public function changeContactView() {
    return view('admin.admin_panel.update-contact')->with('admin', Admin::admin());;
  }
  public function changeContactAction(Request $request) {
    $this->validate($request, [
      'new_phone' => 'required|max:11|min:10',
      'new_address' => 'required|min:4|max:50',
      'new_email' => 'required|min:10|max:100|email',
    ]);

    Admin::where('admin_id', Admin::admin()->admin_id)->update([
      'admin_phone' => $request->input('new_phone'),
      'admin_address' => $request->input('new_address'),
      'admin_email' => $request->input('new_email'),
    ]);

    $request->session()->flash('updating_msg', 'Contact Information has been updated successfully');
    return redirect()->route('admin-profile');
  }

  public function changePictureView() {
    return view('admin.admin_panel.update-picture');
  }
  public function changePictureAction(Request $request) {

    $admin = Admin::admin();

    $this->validate($request, [
      'admin_picture' => 'required'
    ]);

    $name = time() . "_" . rand() . '__' . $admin->admin_username . '__' . '.' . $request->file('admin_picture')->extension();
    $request->file('admin_picture')->move(public_path('admins_pics'), $name);

    Admin::where('admin_id', $admin->admin_id)->update([
      'admin_picture' => url('admins_pics') . '/' . $name
    ]);

    $request->session()->flash('updating_msg', 'Profile picture was updated successfully');
    return redirect()->route('admin-profile');

  }

  // {/admin/admins} route

  public function adminsIndex() {
    if (Admin::admin()->admin_role != 2) {
      return redirect()->route('admin-dashboard');
    } elseif (Admin::admin()->admin_role == 2) {
      return view('admin.admins.admins')->with('admins', Admin::getAllAdmins());
    }
  }
  public function adminsActions(Request $request) {

    if ($request->has('apply_disable_all')) {
      Admin::disableAllAdmins();
      $request->session()->flash('admins_msg', 'All Admins has been disabled for now. Now other admins can not login');
      return redirect()->route('admins-index');

    } elseif ($request->has('apply_allow_all')) {
      Admin::allowAllAdmins();
      $request->session()->flash('admins_msg', 'All Admins has been allowed. Now all admins can login');
      return redirect()->route('admins-index');

    } elseif ($request->has('apply_super_all')) {
      Admin::superAllAdmins();
      $request->session()->flash('admins_msg', 'All Admins has been supered. They have all roles');
      return redirect()->route('admins-index');

    } elseif ($request->has('apply_delete_all')) {
      Admin::deleteAllAdmins();
      $request->session()->flash('admins_msg', 'All Admins has been deleted');
      return redirect()->route('admins-index');

    } elseif ($request->has('apply_disable')) {
      Admin::disbaleAdmin($request->input('admin_id'));
      $request->session()->flash('admins_msg', 'Selected Admin has been disabled successfully');
      return redirect()->route('admins-index');

    } elseif ($request->has('apply_allow')) {
      Admin::allowAdmin($request->input('admin_id'));
      $request->session()->flash('admins_msg', 'Selected Admin has been allowed successfully');
      return redirect()->route('admins-index');

    } elseif ($request->has('apply_super')) {
      Admin::superAdmin($request->input('admin_id'));
      $request->session()->flash('admins_msg', 'Selected Admin has been supered successfully');
      return redirect()->route('admins-index');

    } elseif ($request->has('apply_delete')) {
      Admin::deleteAdmin($request->input('admin_id'));
      $request->session()->flash('admins_msg', 'Selected Admin has been deleted successfully');
      return redirect()->route('admins-index');
    }

    return view('admin.admins.admins')->with('admins', Admin::getAllAdmins());
  }

  public function addAdminIndex() {
    if (Admin::admin()->admin_role != 2) {
      return redirect()->route('admin-dashboard');
    } elseif (Admin::admin()->admin_role == 2) {
      return view('admin.admins.add');
    }
  }
  public function addAdminAction(Request $request) {

    $this->validate($request, [
      'admin_username' => 'required|min:3|max:20|unique:admins',
      'admin_firstname' => 'required|min:3|max:20',
      'admin_lastname' => 'required|min:3|max:20',
      'admin_phone' => 'required|integer|unique:admins',
      'admin_email' => 'required|min:8|max:100|email|unique:admins',
      'admin_password' => 'required|min:8|max:20',
      'admin_address' => 'required|min:3|max:150',
      'admin_picture' => 'required'
    ]);

    $name = time() . "_" . rand() . '.' . $request->file('admin_picture')->extension();
    $request->file('admin_picture')->move(public_path('admins_pics'), $name);

    Admin::create([
      'admin_username' => $request->input('admin_username'),
      'admin_firstname' => $request->input('admin_firstname'),
      'admin_lastname' => $request->input('admin_lastname'),
      'admin_phone' => $request->input('admin_phone'),
      'admin_email' => $request->input('admin_email'),
      'admin_password' => sha1($request->input('admin_password')),
      'admin_address' => $request->input('admin_address'),
      'admin_picture' => url('admins_pics') . '/' . $name,
      'admin_role' => ($request->has('role')) ? 2 : 1
    ]);

    $request->session()->flash('admins_msg', 'Admin Has been Added Successfully');

    return view('admin.admins.add');
  }

}
