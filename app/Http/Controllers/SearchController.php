<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SearchController extends Controller {

  public function view() {
    $search_di = 'HELLO';
    return view('search')->with('se', $search_di);
  }

}
