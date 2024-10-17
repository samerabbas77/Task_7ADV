<?php

namespace App\Http\Controllers\Api;;

use App\Traits\ApiResponseTrait;
use App\Services\Api\RoleServices;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;

use App\Http\Requests\Role\StoreRoleRequest;

class RoleController extends Controller
{
    use ApiResponseTrait;

    protected $roleService;
    public function __construct(RoleServices $roleService)
    {
        $this->roleService = $roleService;
        $this->middleware(['role:Admin', 'permission:role-list'])->only('index');
        $this->middleware(['role:Admin', 'permission:role-list'])->only('show');
        $this->middleware(['role:Admin', 'permission:role-create'])->only(['store']);
        $this->middleware(['role:Admin', 'permission:role-edit'])->only(['update']);
        $this->middleware(['role:Admin', 'permission:role-delete'])->only('destroy');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::orderBy('id','DESC')->paginate(5);
        return $this->success($roles,'Show All roles successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoleRequest $request)
    {
        $data = $request->validated();
        $roles = $this->roleService->createRole( $data);
        return $this->success($roles,'Store role Successfully',200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $role = $this->roleService->showRole($id);
        return $this->success($role,'Show role Successfully',200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreRoleRequest $request, string $id)
    {
        $data = $request->validated();
        $role = $this->roleService->updateRole( $data,  $id);

        return $this->success($role,'Update role Successfully',200);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->roleService->deleteRole($id);
        return $this->success(null,'Delete role Successfully',200);
    }
}
