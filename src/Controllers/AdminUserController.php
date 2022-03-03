<?php

namespace YK\Basic\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use YK\Basic\Extensions\AdminUserFactory;
use YK\Basic\Requests\AdminUser\CreateOrUpdateRequest;
use YK\Basic\Resources\AdminUser as AdminUserResource;
use YK\Basic\Resources\AdminUserCollection;
use YK\Basic\Resources\RoleCollection;

class AdminUserController extends Controller
{
    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $adminUserModel;

    public function __construct()
    {
        $this->adminUserModel = AdminUserFactory::adminUser();
    }

    /**
     * @author nash< >
     * @param Request $request
     * @return AdminUserCollection
     */
    public function index(Request $request)
    {
        return new AdminUserCollection($this->adminUserModel->where(request_like_sect(['username','name', 'email']))->paginate());
    }

    /**
     * @author nash< >
     * @param $id
     * @return AdminUserResource
     */
    public function show($id)
    {
        return new AdminUserResource($this->adminUserModel->findOrFail($id));
    }

    /**
     * @author nash< >
     * @param CreateOrUpdateRequest $request
     * @return Response
     */
    public function store(CreateOrUpdateRequest $request)
    {
        $data = request_intersect([
            'username','name', 'mobile','email', 'password'
        ]);
        $data['password'] = bcrypt($data['password']);

        $this->adminUserModel->create($data);

        return $this->created();
    }

    /**
     * @author nash< >
     * @param CreateOrUpdateRequest $request
     * @param $id
     * @return Response
     */
    public function update(CreateOrUpdateRequest $request, $id)
    {
        $adminUser = $this->adminUserModel->findOrFail($id);

        $data = request_intersect([
            'name','email','mobile'
        ]);

        if (empty($request->filled('mobile'))) {
            $data['mobile'] = '';
        }

        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $adminUser->fill($data);
        $adminUser->save();

        return $this->noContent();
    }

    /**
     * @author nash< >
     * @param $id
     * @return Response
     */
    public function destroy($id)
    {
        $adminUser = $this->adminUserModel->findOrFail($id);

        $adminUser->delete();

        return $this->noContent();
    }

    /**
     * @author nash<nash91@foxmail.com>
     * @param $id
     * @param $provider
     * @return RoleCollection
     */
    public function roles($id, $provider)
    {
        $user = $this->getProviderModel($provider)->findOrFail($id);

        return new RoleCollection($user->roles);
    }

    /**
     * @author nash<>
     * @param $id
     * @param $provider
     * @param Request $request
     * @return Response
     */
    public function assignRoles($id, $provider, Request $request)
    {
        $user = $this->getProviderModel($provider)->findOrFail($id);

        $user->syncRoles($request->input('roles', []));

        return $this->noContent();
    }

    /**
     * @param $provider
     * @return Illuminate\Foundation\Auth\User
     */
    private function getProviderModel($provider)
    {
        return app(config('auth.providers.' . $provider . '.model'));
    }

    /**
     * @author nash< >
     *
     * remove permission cache
     */
    public function clearCache()
    {
        if (!Auth::user()->can("role.assign-permissions"))
            throw new \Exception("User does not have the right permissions.");
        $cacheName=config('permission.cache.key');
        if (empty($cacheName))
            $cacheName='spatie.permission.cache';
        app()['cache']->forget($cacheName);
        return $this->noContent();
    }
}
