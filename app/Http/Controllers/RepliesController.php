<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Replies;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RepliesController extends Controller
{

  public function repliesOfReportIndex($report_id) {
    $allReplies = Replies::join('users', 'reports_replies.reply_for_user', 'users.user_id')
    ->join('reports', 'reports_replies.reply_for_report', 'reports.report_id')
    ->where('reports_replies.reply_for_report', $report_id)
    ->paginate(4);
    return view('admin.reports.replies')->with([
      'replies' => $allReplies,
      'report_id' => $report_id,
    ]);
  }

  public function repliesOfReportActions($report_id, Request $request) {

    if ($request->has('apply_delete_all')) {
      DB::table('reports_replies')->where('reply_for_report', $report_id)->delete();
      $request->session()->flash('reports_msg', 'All Replies for selected report has been deleted');
    } elseif ($request->has('apply_delete')) {
      DB::table('reports_replies')->where('reply_id', $request->input('reply_id'))->delete();
      $request->session()->flash('reports_msg', 'Selected reply has been deleted successfully');
    }
    return redirect()->route('report-replies-index', [$report_id]);
  }

}
