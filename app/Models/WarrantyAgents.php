<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class WarrantyAgents extends Model
{
  use HasFactory;
  protected $table = 'warranty_agents';
  protected $primaryKey = 'agent_id';
  protected $fillable = ['agent_username', 'agent_name', 'agent_company', 'agent_about', 'agent_trusted_level', 'agent_icon'];
  public $timestamps = false;

  public static function getAgents() {
    return self::where('agent_status', 1)->get();
  }

  public static function agents($paginate = 10) {
    return self::where('agent_status', 1)->paginate($paginate);
  }

  public static function countAgents(): int {
    return count(self::getAgents());
  }

  public static function getProductsOfAgent($agent_id, $paginate = 10): \Illuminate\Contracts\Pagination\LengthAwarePaginator {

    return DB::table('products')
      ->join('warranty_agents', 'products.agent', 'warranty_agents.agent_id')
      ->where([
        ['products.agent', $agent_id],
        ['products.deleted', 0],
      ])
      ->paginate($paginate);

  }

  public static function countProductsOfAgent($agent_id): int {
    return DB::table('products')->where('agent', $agent_id)->count();
  }

  // Deleted Agents

  public static function getDeleted($paginate = 10) {
    return self::where('agent_status', 0)->paginate($paginate);
  }

  public static function countDeleted() {
    return self::where('agent_status', 0)->count();
  }

}
