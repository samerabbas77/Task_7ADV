<?php

namespace App\Services\Api;

use Exception;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;
use Illuminate\Validation\ValidationException;

class RoleServices
{
    /**
     * create Role
     * @param array $data
     * @return Role|\Spatie\Permission\Contracts\Role
     */
    public function createRole(array $data)
    {
        try {

            $role = Role::create([
                'name' => $data['name'],
            ]);


            if (isset($data['permission'])) {
                $role->syncPermissions($data['permission']);
            }

            return $role;
        } catch (Exception $e) {
            Log::error($e);
            throw ValidationException::withMessages(['error' => 'Unable to create role at this time. Please try again later.']);
        }
    }
//..............................................................................................
//..............................................................................................
    /**
     * show Role
     * @param string $id
     * @throws \Exception
     * @return array
     */
    public function showRole(string $id)
    {
        try {
            $role = Role::findOrFail($id);
            $rolePermissions = Permission::join("role_has_permissions", "role_has_permissions.permission_id", "=", "permissions.id")
                ->where("role_has_permissions.role_id", $id)
                ->get();

            return [
                'role' => $role,
                'rolePermissions' => $rolePermissions
            ];
        } catch (Exception $e) {
            Log::error($e);
            throw new Exception('Unable to retrieve role details at this time. Please try again later.');
        }
    }

//..............................................................................................
//..............................................................................................
    /**
     * update Role
     * @param array $data
     * @param string $id
     * @return Role|Role[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function updateRole(array $data, string $id)
    {
        try {

            $role = Role::findOrFail($id);
            $role->name = $data['name'];
            $role->save();
            if (isset($data['permission'])) {
                $role->syncPermissions($data['permission']);
            }

            return $role;
        } catch (Exception $th) {
            Log::error($th);
            throw ValidationException::withMessages(['error' => 'Unable to update role at this time. Please try again later.']);
        }


    }
//..............................................................................................
//..............................................................................................

    public function deleteRole(string $id, $newRoleName = 'Customer')
    {
        try {
            $role = Role::findOrFail($id);
            $roleName = $role->name;
            $this->reassignRoleToUsers($roleName, $newRoleName);
            $role->delete();
            return true;
        } catch (Exception $th) {
            Log::error($th);
            throw new Exception('Unable to delete role at this time. Please try again later.');
        }
    }
//..............................................................................................
//..............................................................................................
    private function reassignRoleToUsers($deletedRoleName, $newRoleName)
    {
        try {
            $users = User::role($deletedRoleName)->get();
            foreach ($users as $user) {
                $user->syncRoles([$newRoleName]);
            }
        } catch (Exception $th) {
            Log::error($th);
            throw new Exception('Unable to reassign roles at this time. Please try again later.');
        }
    }
}