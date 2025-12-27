<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Task;
use App\Models\Campaign;
use App\Models\FieldReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use App\Models\ClientEmployee;
use App\Models\Branch;
use App\Models\City;
use App\Models\Classification;
use Illuminate\Support\Facades\Auth;

class SyncController extends Controller
{
    /**
     * Push local changes to server.
     */
    public function push(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'entity' => 'required|in:clients,tasks,campaigns,field_reports,client_employees,branches',
            'operation' => 'required|in:create,update,delete',
            'data' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $entity = $request->entity;
            $operation = $request->operation;
            $data = $request->data;
            $result = null;

            switch ($entity) {
                case 'clients':
                    $result = $this->syncClient($operation, $data);
                    break;
                case 'tasks':
                    $result = $this->syncTask($operation, $data);
                    break;
                case 'campaigns':
                    $result = $this->syncCampaign($operation, $data);
                    break;
                case 'field_reports':
                    $result = $this->syncFieldReport($operation, $data);
                    break;
                case 'client_employees':
                    $result = $this->syncClientEmployee($operation, $data);
                    break;
                case 'branches':
                    $result = $this->syncBranch($operation, $data);
                    break;
            }

            DB::commit();

            return response()->json([
                'message' => 'Sync successful',
                'data' => $result,
                'sync_status' => 'synced'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Sync failed',
                'error' => $e->getMessage(),
                'sync_status' => 'failed'
            ], 500);
        }
    }

    /**
     * Pull server changes to local.
     */
    public function pull(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'last_sync' => 'nullable|date',
            'entities' => 'nullable|array',
            'entities.*' => 'in:clients,tasks,campaigns,field_reports,client_employees,branches,cities,classifications,categories,items,inventory_assignments,inventory_distributions,users',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $lastSync = $request->last_sync ?? now()->subYears(10);
        $entities = $request->entities ?? [
            'clients',
            'tasks',
            'campaigns',
            'field_reports',
            'client_employees',
            'branches',
            'cities',
            'classifications',
            'categories',
            'items',
            'inventory_assignments',
            'inventory_distributions',
            'users'
        ];

        $data = [];

        $user = Auth::user();

        if (in_array('clients', $entities)) {
            $query = Client::where('updated_at', '>', $lastSync);
            // TEMPORARY: Disable scoping to allow all users to see all clients
            if (!$user->hasRole(['super_admin', 'manager'])) {
                $query->where(function ($q) use ($user) {
                    $q->where('created_by', $user->id)
                        ->orWhere('agent_id', $user->id);
                });
            }
            $data['clients'] = $query->get();
        }

        if (in_array('tasks', $entities)) {
            $query = Task::where('updated_at', '>', $lastSync);
            // TEMPORARY: Disable scoping
            // if (!$user->hasRole(['super_admin', 'manager'])) {
            //     $query->where('assignee_id', $user->id);
            // }
            $data['tasks'] = $query->get();
        }

        if (in_array('campaigns', $entities)) {
            // Campaigns might be public or assigned. For now, let's assume all agents see active campaigns?
            // Or only created by them? The requirement says "assigned to".
            // Campaigns usually don't have a single assignee like tasks.
            // Let's keep campaigns global for now unless specified otherwise, or scope by creator.
            $data['campaigns'] = Campaign::where('updated_at', '>', $lastSync)->get();
        }

        if (in_array('field_reports', $entities)) {
            $query = FieldReport::where('updated_at', '>', $lastSync);
            // TEMPORARY: Disable scoping
            // if (!$user->hasRole(['super_admin', 'manager'])) {
            //     $query->where('reporter_id', $user->id);
            // }
            $data['field_reports'] = $query->get();
        }

        if (in_array('client_employees', $entities)) {
            // Client employees belong to clients. We should probably scope them to clients the user can see.
            // This is a bit more complex query.
            // For simplicity/performance, maybe we just return all updated client employees?
            // Or join with clients table.
            $query = ClientEmployee::where('updated_at', '>', $lastSync);
            // TEMPORARY: Disable scoping
            // if (!$user->hasRole(['super_admin', 'manager'])) {
            //     $query->whereHas('client', function ($q) use ($user) {
            //         $q->where('created_by', $user->id)
            //             ->orWhere('agent_id', $user->id);
            //     });
            // }
            $data['client_employees'] = $query->get();
        }

        if (in_array('branches', $entities)) {
            $data['branches'] = Branch::where('updated_at', '>', $lastSync)->get();
        }

        if (in_array('cities', $entities)) {
            $data['cities'] = City::where('updated_at', '>', $lastSync)->get();
        }

        if (in_array('classifications', $entities)) {
            $data['classifications'] = Classification::where('updated_at', '>', $lastSync)->get();
        }

        // --- Inventory Sync ---

        if (in_array('categories', $entities)) {
            $data['categories'] = \App\Models\Category::where('updated_at', '>', $lastSync)->get();
        }

        if (in_array('items', $entities)) {
            $data['items'] = \App\Models\Item::where('updated_at', '>', $lastSync)->get();
        }

        if (in_array('inventory_assignments', $entities)) {
            $data['inventory_assignments'] = \App\Models\InventoryAssignment::where('updated_at', '>', $lastSync)->get();
        }

        if (in_array('inventory_distributions', $entities)) {
            $data['inventory_distributions'] = \App\Models\InventoryDistribution::where('updated_at', '>', $lastSync)->get();
        }

        if (in_array('users', $entities)) {
            // Return basic user info for dropdowns
            $data['users'] = \App\Models\User::where('updated_at', '>', $lastSync)
                ->get(['id', 'name', 'email', 'updated_at']);
        }

        return response()->json([
            'message' => 'Pull successful',
            'data' => $data,
            'sync_timestamp' => now()->toIso8601String()
        ]);
    }

    /**
     * Resolve sync conflict.
     */
    public function resolveConflict(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'entity' => 'required|in:clients,tasks,campaigns,field_reports,client_employees,branches',
            'entity_id' => 'required',
            'resolution' => 'required|in:server_wins,client_wins,merge',
            'data' => 'required_if:resolution,client_wins,merge|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $entity = $request->entity;
            $entityId = $request->entity_id;
            $resolution = $request->resolution;
            $data = $request->data;

            $model = $this->getModel($entity);
            $record = $model::find($entityId);

            if (!$record) {
                return response()->json([
                    'message' => 'Record not found'
                ], 404);
            }

            switch ($resolution) {
                case 'server_wins':
                    // Do nothing, server version is kept
                    $result = $record;
                    break;

                case 'client_wins':
                    // Update with client data
                    $record->update($data);
                    $result = $record->fresh();
                    break;

                case 'merge':
                    // Merge client data with server data
                    $merged = array_merge($record->toArray(), $data);
                    $record->update($merged);
                    $result = $record->fresh();
                    break;
            }

            return response()->json([
                'message' => 'Conflict resolved',
                'data' => $result,
                'resolution' => $resolution
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Conflict resolution failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sync client entity.
     */
    private function syncClient($operation, $data)
    {
        switch ($operation) {
            case 'create':
                return Client::create($data);
            case 'update':
                $client = Client::find($data['id']);
                if ($client) {
                    // Enforce 48-hour edit rule
                    if ($client->created_at->diffInHours(now()) > 48 && !Auth::user()->hasRole(['super_admin', 'manager'])) {
                        throw new \Exception('Cannot edit client after 48 hours');
                    }

                    $client->update($data);
                    return $client->fresh();
                }
                throw new \Exception('Client not found');
            case 'delete':
                $client = Client::find($data['id']);
                if ($client) {
                    $client->delete();
                    return ['deleted' => true];
                }
                throw new \Exception('Client not found');
        }
    }

    /**
     * Sync task entity.
     */
    private function syncTask($operation, $data)
    {
        switch ($operation) {
            case 'create':
                return Task::create($data);
            case 'update':
                $task = Task::find($data['id']);
                if ($task) {
                    $task->update($data);
                    return $task->fresh();
                }
                throw new \Exception('Task not found');
            case 'delete':
                $task = Task::find($data['id']);
                if ($task) {
                    $task->delete();
                    return ['deleted' => true];
                }
                throw new \Exception('Task not found');
        }
    }

    /**
     * Sync campaign entity.
     */
    private function syncCampaign($operation, $data)
    {
        switch ($operation) {
            case 'create':
                return Campaign::create($data);
            case 'update':
                $campaign = Campaign::find($data['id']);
                if ($campaign) {
                    $campaign->update($data);
                    return $campaign->fresh();
                }
                throw new \Exception('Campaign not found');
            case 'delete':
                $campaign = Campaign::find($data['id']);
                if ($campaign) {
                    $campaign->delete();
                    return ['deleted' => true];
                }
                throw new \Exception('Campaign not found');
        }
    }

    /**
     * Sync field report entity.
     */
    private function syncFieldReport($operation, $data)
    {
        switch ($operation) {
            case 'create':
                return FieldReport::create($data);
            case 'update':
                $report = FieldReport::find($data['id']);
                if ($report) {
                    $report->update($data);
                    return $report->fresh();
                }
                throw new \Exception('Field report not found');
            case 'delete':
                $report = FieldReport::find($data['id']);
                if ($report) {
                    $report->delete();
                    return ['deleted' => true];
                }
                throw new \Exception('Field report not found');
        }
    }

    /**
     * Sync client employee entity.
     */
    private function syncClientEmployee($operation, $data)
    {
        switch ($operation) {
            case 'create':
                return ClientEmployee::create($data);
            case 'update':
                $employee = ClientEmployee::find($data['id']);
                if ($employee) {
                    $employee->update($data);
                    return $employee->fresh();
                }
                throw new \Exception('Client employee not found');
            case 'delete':
                $employee = ClientEmployee::find($data['id']);
                if ($employee) {
                    $employee->delete();
                    return ['deleted' => true];
                }
                throw new \Exception('Client employee not found');
        }
    }

    /**
     * Sync branch entity.
     */
    private function syncBranch($operation, $data)
    {
        switch ($operation) {
            case 'create':
                return Branch::create($data);
            case 'update':
                $branch = Branch::find($data['id']);
                if ($branch) {
                    $branch->update($data);
                    return $branch->fresh();
                }
                throw new \Exception('Branch not found');
            case 'delete':
                $branch = Branch::find($data['id']);
                if ($branch) {
                    $branch->delete();
                    return ['deleted' => true];
                }
                throw new \Exception('Branch not found');
        }
    }

    /**
     * Get model class by entity name.
     */
    private function getModel($entity)
    {
        $models = [
            'clients' => Client::class,
            'tasks' => Task::class,
            'campaigns' => Campaign::class,
            'field_reports' => FieldReport::class,
            'client_employees' => ClientEmployee::class,
            'branches' => Branch::class,
        ];

        return $models[$entity] ?? null;
    }

    /**
     * Get sync status.
     */
    public function status()
    {
        $stats = [
            'clients_count' => Client::count(),
            'tasks_count' => Task::count(),
            'campaigns_count' => Campaign::count(),
            'field_reports_count' => FieldReport::count(),
            'client_employees_count' => ClientEmployee::count(),
            'branches_count' => Branch::count(),
            'cities_count' => City::count(),
            'classifications_count' => Classification::count(),
            'last_update' => DB::table('clients')
                ->union(DB::table('tasks'))
                ->union(DB::table('campaigns'))
                ->union(DB::table('field_reports'))
                ->union(DB::table('client_employees'))
                ->union(DB::table('branches'))
                ->max('updated_at'),
        ];

        return response()->json($stats);
    }
}
