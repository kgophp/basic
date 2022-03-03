<?php

namespace YK\Basic\Controllers;


use Illuminate\Http\Request;
use YK\Basic\Models\PermissionGroup;
use YK\Basic\Requests\Permission\CreateOrUpdateRequest;
use YK\Basic\Resources\PermissionCollection;
use Spatie\Permission\Exceptions\PermissionAlreadyExists;
use YK\Basic\Models\Permission;
use YK\Basic\Resources\Permission as PermissionResource;
use YK\Basic\Resources\BaseCollection;
use Illuminate\Support\Facades\Auth;

class PermissionController extends Controller
{
    /**
     * @author nash< >
     * @param Request $request
     * @return PermissionCollection
     */
    public function index(Request $request)
    {
        $pgIdArr = [];
        $pgIds = [];
        $paramPgId = $request->get('pg_id');
        if(!empty($paramPgId)){
            $reqPgId = end($paramPgId);
            $pgIdArr = PermissionGroup::query()->whereRaw('FIND_IN_SET('.$reqPgId.',parent_ids)')->get();
            array_push($pgIds,$reqPgId);
        }

        if(!empty($pgIdArr)){
            $pgIdArr = $pgIdArr->toArray();
            foreach ($pgIdArr as $pgId){
                array_push($pgIds,$pgId['id']);
            }
        }
        $permissions =tap(Permission::latest(), function ($query) use($pgIds){
            $query->where(request_intersect(['guard_name']))
            ->where(request_like_sect(['name']));
            if(!empty($pgIds)){
                $query = $query->whereIn('pg_id',$pgIds);
            }

        })->with('group')->paginate();

        return new PermissionCollection($permissions);
    }

    /**
     * @author nash< >
     * @param $id
     * @return PermissionResource
     */
    public function show($id)
    {
        return new PermissionResource(Permission::query()->findOrFail($id));
    }

    /**
     * @author nash< >
     * @param CreateOrUpdateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateOrUpdateRequest $request)
    {
        $attributes = request_intersect([
            'pg_id', 'name', 'guard_name', 'display_name', 'icon', 'sequence', 'description'
        ]);

        if(is_array($attributes['pg_id'])){
            $attributes['pg_id'] = end($attributes['pg_id']);
        }
        $attributes['created_name'] = Auth::user()->name;

        Permission::create($attributes);

        return $this->created();
    }

    /**
     * @author nash< >
     * @param CreateOrUpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function update(CreateOrUpdateRequest $request, $id)
    {

        $permission = Permission::query()->findOrFail($id);
        $attributes = request_intersect([
            'pg_id', 'name', 'guard_name', 'display_name', 'icon', 'sequence', 'description'
        ]);
        $attributes['updated_name'] = Auth::user()->name;
        if(is_array($attributes['pg_id'])){
            $attributes['pg_id'] = end($attributes['pg_id']);
        }
        $isset = Permission::query()
            ->where(['name' => $attributes['name'], 'guard_name' => $attributes['guard_name']])
            ->where('id', '!=', $id)
            ->count();

        if ($isset) {
            throw PermissionAlreadyExists::create($attributes['name'], $attributes['guard_name']);
        }

        $permission->update($attributes);

        return $this->noContent();
    }

    /**
     * @author nash< >
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        permission::query()->findOrFail($id)->delete();

        return $this->noContent();
    }

    /**
     * @author nash< >
     * @return \Illuminate\Http\JsonResponse
     */
    public function allUserPermission()
    {
        return response()->json(['data' => Auth::user()->getAllPermissions()->pluck('name')]);
    }
}
