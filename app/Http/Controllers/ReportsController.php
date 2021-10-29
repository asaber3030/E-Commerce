<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Replies;
use App\Models\Reports;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{

  public function reportsIndex() {
    $reports = Reports::allReports(10);
    return view('admin.reports.index')->with('reports', $reports);
  }

  public function reportsActions(Request $request): \Illuminate\Http\RedirectResponse {
    if ($request->has('apply_delete_all')) {
      DB::table('reports')->select('*')->delete();
      $request->session()->flash('reports_msg', 'All Reports have been deleted successfully.');
    } elseif ($request->has('apply_delete')) {
      DB::table('reports')->where('report_id', $request->input('report_id'))->delete();
      $request->session()->flash('reports_msg', 'Selected report was deleted successfully');
    }
    return redirect()->route('reports-index');
  }

  public function reportReplyIndex($report_id) {
    $getReport = Reports::find($report_id);
    $user = User::find($getReport->report_from_user);
    return view('admin.reports.reply')->with([
      'user' => $user,
      'report' => $getReport
    ]);
  }

  public function reportReplyAction($report_id, Request $request) {
    $getReport = Reports::find($report_id);

    $this->validate($request, [
      'reply_content' => 'required|min:100|max:255',
    ]);

    Replies::create([
      'reply_content' => $request->input('reply_content'),
      'reply_for_user' => $getReport->report_from_user,
      'reply_for_report' => $getReport->report_id,
    ]);

    $request->session()->flash('reports_msg', 'Reply was sent to user successfully');
    return redirect()->route('reports-index');
  }


}
