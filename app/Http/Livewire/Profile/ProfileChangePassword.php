<?php

namespace App\Http\Livewire\Profile;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class ProfileChangePassword extends Component {

  public $user;

  public $old_password;
  public $new_password;
  public $confirm_password;

  public $updateMsg;

  public $array = [];


  public function __construct($id = null) {
    parent::__construct($id);
    $this->user = \Auth::user();
  }
  public function render() {
    return view('livewire.profile.profile-change-password');
  }

  public function changePassword() {
    $errors = $this->validate([
      'old_password' => 'required',
      'new_password' => 'required|min:8|max:20|different:old_password|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#%]).*$/|',
      'confirm_password' => 'required|same:new_password'
    ]);

    if (Hash::check($this->old_password, $this->user->password)) {
      User::where('user_id', $this->user->user_id)->update([
        'password' => Hash::make($this->new_password)
      ]);
      $this->updateMsg = "Password has been changed successfully!";
    } else {
      $this->updateMsg = 'Your Old Password does not match!';
    }
  }
}
