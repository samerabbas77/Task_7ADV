<?php

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
       
          Route::apiResource('/user',UserController::class);

          Route::get('Users/assigned-tasks', [UserController::class, 'getUsersWithAssignedTasks']);  

          Route::get('/user/trash',[UserController::class,'trashed']);

          Route::post('/user/restore/{id}',[UserController::class,'restore']);

          Route::delete('/user/force/{id}',[UserController::class,'forceDelte']);

          //.........................End of User Route.....................................
          /**
           * Task Route 
           */
          Route::apiResource('tasks',TaskController::class);

          Route::get('tasks?type=&status=&assigned_to=&due_date=&priority=&depends_on=',[TaskController::class,'getAllTaskswithFilters']);
         
          Route::get('tasks?status=Blocked',[TaskController::class,'getAllBluckedTasks()']);
         
          //soft Delete

          Route::delete('tasks/{task}/forceDelete',[TaskController::class,'forceDestroy']);

          Route::get('tasks/trashed',[TaskController::class,'trashed']);

          Route::get('tasks/{task}/restore',[TaskController::class,'restore']);

          //.....End of soft Delete

          Route::put('/tasks/{task}/status',[TaskController::class,'updateStatus']);

          Route::post('/tasks/{task}/assign',[TaskController::class,'assignTask']);

          Route::put('/tasks/{task}/reAssign',[TaskController::class,'reAssignTask']);

          Route::post('/tasks/{task}/attachments',[TaskController::class,'uploadAttachment']);

          Route::post('/tasks/{task}/comments',[TaskController::class,'addComment']);

          Route::get('/reports/daily-tasks',[TaskController::class,'taskReports']);
 

        });
