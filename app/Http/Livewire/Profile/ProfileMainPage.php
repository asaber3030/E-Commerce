<?php

namespace App\Http\Livewire\Profile;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class ProfileMainPage extends Component {

  public $user;

  public $username;
  public $firstname;
  public $lastname;
  public $email;
  public $phone_number;

  public $updateMsg = '';

  public function __construct($id = null) {
    parent::__construct($id);
    $this->user = \Auth::user();
    $this->username = $this->user->username;
    $this->firstname = $this->user->firstname;
    $this->lastname = $this->user->lastname;
    $this->email = $this->user->email;
    $this->phone_number = $this->user->phone_number;


  }

  public function render() {
    return view('livewire.profile.profile-main-page');
  }

  public function changeInfo() {

    $this->validate([
      'username' => 'required',
      'firstname' => 'required|min:3|max:25',
      'lastname' => 'required|min:3|max:25',
      'email' => 'required|email',
      'phone_number' => 'required'
    ]);

    User::where('user_id', $this->user->user_id)->update([
      'username' => $this->username,
      'firstname' => $this->firstname,
      'lastname' => $this->lastname,
      'email' => $this->email,
      'phone_number' => $this->phone_number
    ]);

    session()->flash('msg', 'Personal Information updated Successfully');

    redirect()->route('update-account');

    $this->updateMsg = "Personal Information updated Successfully";

  }

}
