<?php

use App\Http\Controllers\Api\RoleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Auth\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::post('/login', [AuthController::class, 'login']);


Route::group([
    'middleware' => ['auth:api', 'throttle:60,1','security_middleware']
    
  ], function ($router) {
         //Authentecation
         Route::post('/logout', [AuthController::class, 'logout']);
         Route::post('/refresh', [AuthController::class, 'refresh']);
         Route::post('/me', [AuthController::class, 'info']);

               /**
         * User Management Routes for admin
         *
         * These routes handle User management operations.
         */
        //User Api routes.................

          Route::get('/user', [UserController::class, 'index'])
              ->middleware('permission:user-list');
          
          Route::get('/user/{user}', [UserController::class, 'show'])
              ->middleware('permission:user-view');
          
          Route::post('/user', [UserController::class, 'store'])
              ->middleware('permission:user-create');
          
          Route::put('/user/{user}', [UserController::class, 'update'])
              ->middleware('permission:user-edit');
          
          Route::delete('/user/{user}', [UserController::class, 'destroy'])
              ->middleware('permission:user-delete');

        //End of User Api routes.....................

          Route::get('Users/assigned-tasks', [UserController::class, 'getUsersWithAssignedTasks'])
              ->middleware('permission:user-list');  

          Route::get('/user-trash',[UserController::class,'trashed'])
              ->middleware('permission:user_trash');

          Route::post('/user/restore/{id}',[UserController::class,'restore'])
              ->middleware('permission:user_restore');

          Route::delete('/user/force/{id}',[UserController::class,'forceDelete'])
              ->middleware('permission:user_forceDelete');

          //.........................End of User Route.....................................
          /**
           * Task Route 
           */
       
          //Task Api Route....................................
          Route::get('/tasks', [TaskController::class, 'index'])
              ->middleware('permission:task-list');

          Route::get('/tasks/{task}', [TaskController::class, 'show'])
              ->middleware('permission:task-view');

          Route::post('/tasks', [TaskController::class, 'store'])
              ->middleware('permission:task-create');

          Route::put('/tasks/{task}', [TaskController::class, 'update'])
              ->middleware('permission:task-edit');

          Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])
              ->middleware('permission:task-delete');

          //End of Task Api Route................................

          Route::get('/tasks-filter',[TaskController::class,'getAllTaskswithFilters'])
             ->middleware('permission:task-list');
         
          Route::get('/tasks-blocked',[TaskController::class,'getAllBluckedTasks'])
             ->middleware('permission:task-list');
         
          //soft Delete

          Route::delete('/tasks/{task}/forceDelete',[TaskController::class,'forceDestroy'])
             ->middleware('permission:task_forceDelte');

          Route::get('/tasks-trash',[TaskController::class,'trashed'])
             ->middleware('permission:task_trash');

          Route::get('/tasks/{task}/restore',[TaskController::class,'restore'])
             ->middleware('permission:task_restore');

          //.....End of soft Delete

          Route::put('/tasks/{task}/status',[TaskController::class,'updateStatus'])
             ->middleware('permission:update_status');

          Route::post('/tasks/{task}/assign',[TaskController::class,'assignTask'])
             ->middleware('permission:assign_task');

          Route::put('/tasks/{task}/reAssign',[TaskController::class,'reAssignTask'])
             ->middleware('permission:reAssign_task');

          Route::post('/tasks/{task}/attachments',[TaskController::class,'uploadAttachment'])
             ->middleware('permission:upload_attachment');

          Route::post('/tasks/{task}/comments',[TaskController::class,'addComment'])
             ->middleware('permission:add_comment');

          Route::get('/reports/daily-tasks',[TaskController::class,'taskReports'])
             ->middleware('permission:task_reports');
 
          /**
           * role Routes
           */

            Route::get('/role', [RoleController::class, 'index'])
              ->middleware('permission:role-list');

            Route::get('/role/{id}', [RoleController::class, 'show'])
              ->middleware('permission:role-list');

            Route::post('/role', [RoleController::class, 'store'])
               ->middleware('permission:role-create');

            Route::put('/role/{id}', [RoleController::class, 'update'])
               ->middleware('permission:role-edit');

            Route::delete('/role/{id}', [RoleController::class, 'destroy'])
               ->middleware('permission:role-delete');

            Route::get('/test/{id}',[TaskController::class,'test']);
        });
