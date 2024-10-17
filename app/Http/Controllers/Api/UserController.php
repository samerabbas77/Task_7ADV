<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Services\Api\UserServices;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;

class UserController extends Controller
{
    use ApiResponseTrait;
    protected $userServices;
    public function __construct(UserServices $userServices)
    {
        $this->userServices = $userServices;
        $this->middleware('permission:product-list|product-create|product-edit|product-delete', ['only' => ['index','show']]);
         $this->middleware('permission:product-create', ['only' => ['create','store']]);
         $this->middleware('permission:product-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:product-delete', ['only' => ['destroy']]);
    }


    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page',10);
        $users = $this->userServices->getAllUser($perPage);

        return $this->success( UserResource::collection($users),'Get all users Successfully',200);
    }


      /**
     * Display a listing of the resource.
     */
    public function getUsersWithAssignedTasks(Request $request)
    {
        $perPage = $request->input('per_page',10);
        $users = $this->userServices->getUsersWithAssignedTasks($perPage,$request->input('status'),$request->input('priority'));

        return $this->success( UserResource::collection($users),'Get all users  with their Tasks Successfully',200);
    }
      

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();
        $user = $this->userServices->storeUser($validated);

        return $this->success(new UserResource($user),'Store user Successfully',200);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user = $this->userServices->getUser( $user);
        return $this->success(new UserResource($user),'Show user Successfully',200);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request,User $user)
    {
        $validated = $request->validated();

        $this->userServices->updateUser($validated,$user);
        return $this->success(new UserResource($user),'Update user Successfully',200);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $this->userServices->deleteUser($user);
        return $this->success(null,'Deleting user Successfully',200);  
    }

    //...............Soft Delete...............................................
   /**
     * Display a paginated listing of the trashed (soft deleted) resources.
     */
    public function trashed()
    {
        $users = $this->userServices->getTrashedUsers();
        return $this->success(UserResource::collection($users));

    }
    /**
     * Restore a trashed (soft deleted) resource by its ID.
     */
    public function restore(string $id)
    {
        $user = $this->userServices->restoreUser($id);
        return $this->successResponse(new UserResource($user), "User restored Successfully");
    }


    /**
     * Permanently delete a trashed (soft deleted) resource by its ID 
     */

     public function forceDelte(string $id)
     {
        $this->userServices->forceDeleteUser($id);
        return $this->successResponse(null, "User Permanently deleted Successfully");

     }
}
