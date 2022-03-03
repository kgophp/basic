<?php

namespace YK\Basic\Controllers;


use Illuminate\Http\Request;
use YK\Basic\Requests\PermissionGroup\CreateOrUpdateRequest;
use YK\Basic\Models\PermissionGroup;
use YK\Basic\Models\Permission;
use YK\Basic\Resources\PermissionGroupCollection;
use YK\Basic\Resources\PermissionGroup as PermissionGroupResource;
use Illuminate\Support\Facades\Auth;

class PermissionGroupController extends Controller
{
    /**
     * @author moell<moell91@foxmail.com>
     * @param Request $request
     * @return PermissionGroupCollection
     */
    public function index(Request $request)
    {
        /* delete by nash 20200521
        $permissionGroups = tap(PermissionGroup::latest(), function ($query) {
            $query->select(['id','parent_id','parent_ids','name as value','name as label','name','sequence','set_by_admin','created_at','updated_at'])->where(request_like_sect(['name']));
        })->orderBy('sequence','asc')->get();
        */
        //add by nash 20200521
        $permissionGroups = tap(PermissionGroup::query(), function ($query) {
            $query->select(['id','parent_id','parent_ids','name as value','name as label','name','sequence','set_by_admin','created_at','updated_at'])->where(request_like_sect(['name']));
        })->orderBy('parent_id','asc')->orderBy('sequence','asc')->get();

        return response()->json(['data' => make_tree($permissionGroups->toArray())]);
    }

    /**
     * @param $guardName
     * @return \Illuminate\Http\JsonResponse
     */
    /*
    public function guardNameForPermissions($guardName)
    {
        $builder=PermissionGroup::query();
        try {
            if (!Auth::user()->isAdministrator())
                $builder = PermissionGroup::query()->where('set_by_admin', '=', 0);
        }catch(\Exception $e){
        }

        //$permissionGroups = PermissionGroup::query()
        $permissionGroups = $builder
            ->with(['permission' => function ($query) use ($guardName) {
                $query->where('guard_name', $guardName);
            }])
            ->get()->filter(function($item)  {
                return count($item->permission) > 0;
            });

        return response()->json([
            'data' => array_values($permissionGroups->toArray())
        ]);
    }*/
    public function guardNameForPermissions($guardName)
    {
        //yeqs 修改于 2020.04.20
        $select = ['id','parent_id','name','name as display_name','set_by_admin','sequence'];
        $builder=PermissionGroup::query()->select($select);
        try {
            if (!Auth::user()->isAdministrator())
                $builder = PermissionGroup::query()->select($select)->where('set_by_admin', '=', 0);
        }catch(\Exception $e){
        }

        //$permissionGroups = PermissionGroup::query()
        $permissionGroups = $builder
            ->with(['permission' => function ($query) use ($guardName) {
                $query->where('guard_name', $guardName)
                    ->orderBy("sequence","asc");
            }])->orderBy("sequence","asc")
            ->get();

        return response()->json(['data' => $this->makePermissionTree($permissionGroups->toArray())]);
    }

    function makePermissionTree(array $list, $parentId = 0) {
        $tree = [];
        if (empty($list)) {
            return $tree;
        }

        $newList = [];
        foreach ($list as $k => $v) {
            $newList[$v['id']] = $v;
            if (isset($newList[$v['id']]['permission'])){
                $newList[$v['id']]['children']=$newList[$v['id']]['permission'];
                unset($newList[$v['id']]['permission']);
            }
        }

        foreach ($newList as $value) {
            if ($parentId == $value['parent_id']) {
                $tree[] = &$newList[$value['id']];
            } elseif (isset($newList[$value['parent_id']])) {
                $newList[$value['parent_id']]['children'][] = &$newList[$value['id']];
            }
        }

        return $tree;
    }

    /**
     * @author moell<moell91@foxmail.com>
     * @param CreateOrUpdateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateOrUpdateRequest $request)
    {
        $partentIds = !empty($this->getTreeParentIds($request->get('parent_id'))) ? $this->getTreeParentIds($request->get('parent_id')) : '';
        PermissionGroup::create(['name'=>$request->get('name'),
            'parent_id' => $request->get('parent_id'),
            'parent_ids'=>$partentIds,
            'sequence'=>!empty($request->get('sequence')) ? $request->get('sequence') : 0,
            'set_by_admin' => $request->get('set_by_admin')
        ]);
        return $this->created();
    }


    private function getTreeParentIds($parentId)
    {
        $parentIds = '';
        if(!empty($parentId)) {
            $parent = PermissionGroup::query()->where(["id" => $parentId])->first();
            if (!empty($parent) && !empty($parent->parent_ids)) {
                $parentIds = $parent->parent_ids . ',' . $parentId;
            } else {
                $parentIds = $parentId;
            }
        }
        return $parentIds;
    }

    /**
     * @author moell<moell91@foxmail.com>
     * @param $id
     * @return PermissionGroupResource
     */
    public function show($id)
    {
        return new PermissionGroupResource(PermissionGroup::findOrFail($id));
    }

    /**
     * @author moell<moell91@foxmail.com>
     * @param CreateOrUpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function update(CreateOrUpdateRequest $request, $id)
    {
        $partentIds = !empty($this->getTreeParentIds($request->get('parent_id'))) ? $this->getTreeParentIds($request->get('parent_id')) : '';
        $updateArr = ['name' => $request->get('name'),
            'parent_id'=>$request->get('parent_id'),
            'parent_ids'=>$partentIds,
            'sequence'=>!empty($request->get('sequence')) ? $request->get('sequence') : 0,
            'set_by_admin' => empty($request->get('set_by_admin'))?0:1
        ];
        $a=PermissionGroup::findOrFail($id)->update($updateArr);

        return $this->noContent();
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $permissionGroup = PermissionGroup::findOrFail($id);

        $isParentPermission = PermissionGroup::query()->where('parent_id',$id)->count();
        if($isParentPermission > 0){
            throw new \Exception('该权限组下包含子元素，不可以删除!');
        }

        if (Permission::query()->where('pg_id', $permissionGroup->id)->count()) {
            return $this->unprocesableEtity([
                'pg_id' => 'Please move or delete the vesting permission.'
            ]);
        }

        $permissionGroup->delete();

        return $this->noContent();
    }




}
