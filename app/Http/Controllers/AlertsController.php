<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Alerts;
use App\Models\DeliveryMen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AlertsController extends Controller {

  public function alertsIndex() {
    $allAlerts = Alerts::where('alert_for_admin',  Admin::admin()->admin_id)->paginate(4);
    return view('admin.alerts.index')->with('alerts', $allAlerts);
  }

  public function alertsActions(Request $request): \Illuminate\Http\RedirectResponse {
    if ($request->has('apply_delete_all')) {
      DB::table('alerts')->where('alert_for_admin', Admin::admin()->admin_id)->delete();
      $request->session()->flash('alerts_msg', 'All Alerts have been deleted successfully.');
    } elseif ($request->has('apply_delete')) {
      DB::table('alerts')->where('alert_id', $request->input('alert_id'))->delete();
      $request->session()->flash('alerts_msg', 'Selected alert was deleted successfully');
    }
    return redirect()->route('alerts-index');
  }

  public function alertsAddIndex() {
    $allDeliveryMen = DeliveryMen::where('delivery_id', '!=', 1)->get();
    return view('admin.alerts.send-alert')->with('delivery', $allDeliveryMen);
  }

  public function alertsAddAction(Request $request) {

    $this->validate($request, [
      'alert_title' => 'required|min:10|max:100',
      'alert_content' => 'required|min:100|max:255',
    ]);

    Alerts::create([
      'alert_title' => $request->input('alert_title'),
      'alert_content' => $request->input('alert_content'),
      'alert_name' => $request->input('alert_name'),
      'alert_for_delivery' => $request->input('alert_for_delivery'),
      'alert_for_admin' => Admin::admin()->admin_id
    ]);

    $request->session()->flash('alerts_msg', 'Delivery man was alerted!');
    return redirect()->route('alerts-index');
  }

}
