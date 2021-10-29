<?php

namespace App\Http\Controllers;

use App\Models\Favs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavsController extends Controller
{

  public function __construct() {
    $this->middleware('auth');
  }

  public function index() {
    return view('favourites');
  }

  public function removeFav(Request $request) {
    $id = Auth::user()->user_id;
    Favs::where('fav_id', $request->input('fav_id'))->delete();
    $request->session()->flash('removed', 'Favourite product was removed');
    return redirect()->route('favourites');
  }

}
