<?php
namespace App\Services\Api;

use Exception;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\Log;
use App\Services\ApiResponseService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserServices
{
   use ApiResponseTrait;

    /**
     * get all user with their tasks and filtter if there status or priotity come from
     * request.
     * @param mixed $perPage
     * @param mixed $status
     * @param mixed $priority
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @return mixed
     */
    public function getAllUser($perPage)
    {
            try {
                return User::paginate($perPage);
            } catch (Exception $e) {
                Log::error("Error while fetch the users".$e->getMessage());
                throw new HttpResponseException($this->error(null, 'there is something wrong in server', 500));
            }
    }

//*********************************************************** 
    public function getUsersWithAssignedTasks(string $perPage,$status =null,$priority =null)
    {
        try {
           // $users = User::with('assignedTasks')->;
           // dd($users);
            $users = User::with('assignedTasks')
                        ->filterTasks($status,$priority)
                        ->paginate($perPage);
            return $users;
        } catch (Exception $e) {
            Log::error("Error while fetch the users".$e->getMessage());
            throw new HttpResponseException($this->error(null, 'there is something wrong in server', 500));
        }
    }
//**********************************************************************************************
// *****************************************************************************************************
    /**
     * store user
     * @param array $data
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @return User|\Illuminate\Database\Eloquent\Model
     */
    public function storeUser(array $data)
    {
        try {
                $user = User::create([
                    'firstName'  =>$data['firstName'],
                    'lastName'   =>$data['lastName'],
                    'email'      =>$data['email'],
                    'password'   =>Hash::make($data['password']),
                ]);
                $user->assignRole($data['role']);

                return $user;
        } catch (Exception $e) {
            Log::error("Error while storing the user".$e->getMessage());
            throw new HttpResponseException($this->error(null, 'there is something wrong in server', 500));
        }
    }
        
   //**********************************************************************************************
// ***************************************************************************************************** 
    /**
     * get one user info with his task
     * @param \App\Models\User $user
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @return object|User|\Illuminate\Database\Eloquent\Model|null
     */
    public function getUser(User $user)
    {
        try {
            if((auth('api')->id() == $user->id )|| (auth('api')->user()->role == 'Admin'))
            {
                $user = $user->load('assignedTasks')->first();
                return $user;
            }else{
                return null;
            }

        } catch (Exception $e) {
            Log::error("Error while fetch the user".$e->getMessage());
            throw new HttpResponseException($this->error(null, 'there is something wrong in server', 500));
        }
    } 
    //**********************************************************************************************
    // ***************************************************************************************************** 

    /**
     * update usr info
     * @param array $validated
     * @param \App\Models\User $user
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @return User
     */
    public function updateUser(array $validated,User $user)
    {
        try {
               $user->firstName = $validated['firstName'] ?? $user->firstName;
               $user->lastName = $validated['lastName'] ?? $user->lastName;
               $user->email = $validated['email'] ?? $user->email;
               $user->password = Hash::make($validated['password']) ?? $user->password;
               $user->role = $validated['role'] ?? $user->role;

               $user->save();
               return $user;
        } catch (Exception $e) {
            Log::error("Error while Updating the user".$e->getMessage());
            throw new HttpResponseException($this->error(null, 'there is something wrong in server', 500));
        }
    }
    //**********************************************************************************************
    // ***************************************************************************************************** 
    /**
     * delete User
     * @param \App\Models\User $user
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @return void
     */
    public function deleteUser(User $user)
    {
        try {
              $user->delete();
        } catch (Exception $e) {
            Log::error("Error while Deleting the user".$e->getMessage());
            throw new HttpResponseException($this->error(null, 'there is something wrong in server', 500));
        }
    }

    //**********************************************************************************************
    // *****************************************************************************************************
    /**
     * fetch all the deleted user
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getTrashedUsers()
    {
        try {
            $users = User::onlyTrashed()->paginate(perPage: 10);
            return $users;
        } catch (Exception $e) {
            Log::error("Error while fetching deleted users".$e->getMessage());
            throw new HttpResponseException($this->error(null, 'there is something wrong in server', 500));
        }
    }

    //**********************************************************************************************
    // *****************************************************************************************************

    public function restoreUser(string $id)
    {
        try {
            $user = User::onlyTrashed()->findOrFail($id);
            $user->restore();
            return $user;
        } catch (ModelNotFoundException $e) {
            Log::error('User not found: ' . $e->getMessage());
            throw new HttpResponseException($this->error(null, 'User not found', 404));
        } catch (Exception $e) {
            Log::error('Error restoring User: ' . $e->getMessage());
            throw new HttpResponseException($this->error(null, 'there is something wrong in server', 500));
        }
    }

     //**********************************************************************************************
    // *****************************************************************************************************
    /**
     * Permanently delete a trashed (soft deleted) resource by its ID
     * @param mixed $id
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @return void
     */
    public function forceDeleteUser($id)
    {
        try {
            $user =User::onlyTrashed()->findOrFail($id);
            $user->forceDelete();
        } catch (ModelNotFoundException $e) {
            Log::error('User not found: ' . $e->getMessage());
            throw new HttpResponseException($this->error(null, 'User not found', 404));
        } catch (Exception $e) {
            Log::error('Error Force Deleting User: ' . $e->getMessage());
            throw new HttpResponseException($this->error(null, 'there is something wrong in server', 500));
        }

    }
}