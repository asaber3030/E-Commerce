<?php

namespace App\Http\Controllers;

use App\Models\Products;
use App\Models\WarrantyAgents;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WarrantyAgentsController extends Controller
{

  public function agentsIndex() {
    $agents = WarrantyAgents::agents();
    return view('admin.agents.agents')->with([
      'agents' => $agents,
      'count' => WarrantyAgents::countAgents()
    ]);
  }
  public function agentsActions(Request $request) {
    if ($request->has('apply_delete')) {
      WarrantyAgents::where('agent_id', $request->input('agent_id'))->update(['agent_status' => 0]);
      $request->session()->flash('agents_msg', 'Selected agent with id: #' . $request->input('agent_id') . ' has been deleted');
    } elseif ($request->has('apply_delete_all')) {
      DB::table('warranty_agents')->update(['agent_status' => 0]);
      $request->session()->flash('agents_msg', 'All Agents has been deleted. You can restore them all again');
    }

    return redirect()->route('agents-index');
  }

  public function addAgentIndex() {
    return view('admin.agents.add');
  }
  public function addAgentAction(Request $request) {
    $this->validate($request, [
      'agent_username' => 'required|min:5|max:50|unique:warranty_agents',
      'agent_name' => 'required|min:10|max:50',
      'agent_company' => 'required|min:5|max:50|unique:warranty_agents',
      'agent_about' => 'required|min:100|max:255',
      'agent_trusted_level' => 'required|numeric',
      'agent_icon' => 'required'
    ]);

    $name = time() . "_" . rand() . '__' . '.' . $request->file('agent_icon')->extension();
    $request->file('agent_icon')->move(public_path('agents_pics'), $name);

    WarrantyAgents::create([
      'agent_username' => $request->input('agent_username'),
      'agent_name' => $request->input('agent_name'),
      'agent_company' => $request->input('agent_company'),
      'agent_about' => $request->input('agent_about'),
      'agent_trusted_level' => $request->input('agent_trusted_level'),
      'agent_icon' => url('agents_pics') . '/' . $name ,
    ]);

    $request->session()->flash('agents_msg', 'Agent Has been added successfully');

    return redirect()->route('agents-index');
  }

  public function agentUpdateIndex($agent_id) {
    if (WarrantyAgents::exists($agent_id)) {
      return view('admin.agents.update')->with('agent', WarrantyAgents::find($agent_id));
    } else {
      return redirect()->route('agents-index');
    }
  }
  public function agentUpdateAction($agent_id, Request $request) {

    $this->validate($request, [
      'agent_username' => 'required|min:5|max:50',
      'agent_name' => 'required|min:10|max:50',
      'agent_company' => 'required|min:5|max:50',
      'agent_about' => 'required|min:100|max:255',
    ]);


    if ($request->has('agent_icon')) {
      $name = time() . "_" . rand() . $agent_id . '__' . '.' . $request->file('agent_icon')->extension();
      $request->file('agent_icon')->move(public_path('agents_pics'), $name);

      WarrantyAgents::where('agent_id', $agent_id)->update([
        'agent_username' => $request->input('agent_username'),
        'agent_name' => $request->input('agent_name'),
        'agent_company' => $request->input('agent_company'),
        'agent_about' => $request->input('agent_about'),
        'agent_icon' => url('agents_pics') . '/' . $name ,
      ]);
    } else {

      WarrantyAgents::where('agent_id', $agent_id)->update([
        'agent_username' => $request->input('agent_username'),
        'agent_name' => $request->input('agent_name'),
        'agent_company' => $request->input('agent_company'),
        'agent_about' => $request->input('agent_about'),
      ]);
    }

    $request->session()->flash('agents_msg', 'Agent Has been updated successfully');

    return redirect()->route('agents-update-index', $agent_id);

  }

  public function productsOfAgentIndex($agent_id) {
    $products = WarrantyAgents::getProductsOfAgent($agent_id);
    return view('admin.agents.products-of')->with([
      'products' => $products,
      'count' => WarrantyAgents::countProductsOfAgent($agent_id),
      'agent' => WarrantyAgents::find($agent_id)
    ]);
  }
  public function productsOfAgentActions($agent_id, Request $request) {
    if ($request->has('delete-selected')) {
      Products::where([
        ['agent', $agent_id],
        ['product_id', $request->input('product_id')],
      ])->update(['deleted' => 1]);
      $request->session()->flash('agents_msg', 'Selected product has been deleted. you can restore later');
    } elseif ($request->has('apply_delete_all')) {
      DB::table('products')->where('agent', $agent_id)->update(['deleted' => 1]);
      $request->session()->flash('agents_msg', 'All products for agent has been deleted');
    }

    return redirect()->route('agents-products-index', [$agent_id]);
  }

  public function deletedAgentsIndex() {
    $agents = WarrantyAgents::getDeleted();
    return view('admin.agents.deleted')->with([
      'agents' => $agents,
      'count' => WarrantyAgents::countDeleted()
    ]);
  }
  public function deletedAgentsAction(Request $request): \Illuminate\Http\RedirectResponse {

    if ($request->has('apply_restore')) {
      WarrantyAgents::where('agent_id', $request->input('agent_id'))->update(['agent_status' => 1]);
      $request->session()->flash('agents_msg', 'Selected agent with id: #' . $request->input('agent_id') . ' has been restored');
    } elseif ($request->has('apply_restore_all')) {
      DB::table('warranty_agents')->update(['agent_status' => 1]);
      $request->session()->flash('agents_msg', 'All Agents has been restored.');
    }

    return redirect()->route('agents-deleted-index');
  }


}
