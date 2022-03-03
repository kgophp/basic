<?php

namespace YK\Basic\Controllers;


use Illuminate\Http\Request;
use YK\Basic\Requests\Menu\CreateOrUpdateRequest;
use YK\Basic\Models\Menu;
use YK\Basic\Resources\Menu as MenuResource;
use Illuminate\Support\Facades\Auth;
use SMartins\PassportMultiauth\Config\AuthConfigHelper;

class MenuController extends Controller
{
    /**
     * @author  nash<  >
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $menus = Menu::query()
            ->where('guard_name', $request->input('guard_name', 'admin'))
            ->orderBy('sequence', 'desc')
            ->get();

        return response()->json(['data' => make_tree($menus->toArray())]);
    }
    /**
     * @author  nash<  >
     * @param CreateOrUpdateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateOrUpdateRequest $request)
    {
        Menu::create($request->all());

        return $this->created();
    }

    /**
     * @author  nash<  >
     * @return \Illuminate\Http\JsonResponse
     */
    public function my()
    {
        $guardName = AuthConfigHelper::getUserGuard(Auth::user());

        $userPermissions = Auth::user()->getAllPermissions()->pluck('name');
        $menus = Menu::query()
            ->where('guard_name', $guardName)
            ->orderBy('sequence', 'desc')
            ->get()
            ->filter(function ($item) use ($userPermissions) {
                return !$item->permission_name || $userPermissions->contains($item->permission_name);
            });

        return response()->json(['data' => make_tree($menus->toArray())]);
    }

    /**
     * @author  nash<  >
     * @param CreateOrUpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function update(CreateOrUpdateRequest $request, $id)
    {
        $menu = Menu::query()->findOrFail($id);

        $menu->update($request->toArray());

        return $this->noContent();
    }

    /**
     * @author  nash<  >
     * @param $id
     * @return MenuResource
     */
    public function show($id)
    {
        return new MenuResource(Menu::query()->findOrFail($id));
    }

    /**
     * @author  nash<  >
     * @param $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $menu = Menu::query()->findOrFail($id);

        if (Menu::query()->where('parent_id', $menu->id)->count()) {
            return $this->unprocesableEtity([
                'parent_id' => 'Please delete the submenu first.'
            ]);
        }

        $menu->delete();

        return $this->noContent();
    }
}
