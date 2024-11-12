# Advanced Task Management API

## Overview
The Advanced Task Management API is designed to facilitate comprehensive task management with features like real-time notifications, task dependencies, and robust security measures. This API enables handling multiple task types, managing task dependencies, and generating performance analytics through periodic reports. Key functionalities include advanced authentication, protection against common security threats, and fine-grained user role and permission management.

---

## Requirements

### Models
1. **Task**
   - Fields: `title`, `description`, `type` (`Bug`, `Feature`, `Improvement`), `status` (`Open`, `In Progress`, `Completed`, `Blocked`), `priority` (`Low`, `Medium`, `High`), `due_date`, `assigned_to` (User ID).
   
2. **Comment**
   - Polymorphic relationship with Task to store task-related comments.

3. **Attachment**
   - Polymorphic relationship to handle file attachments associated with tasks.

4. **TaskStatusUpdate**
   - Tracks changes in task status with a `hasMany` relationship to Task.

5. **User**
   - Manages task assignments and connects tasks with users via a `belongsTo` relationship.

6. **Role**
   - Manages user permissions by defining specific roles for each user.

---

### API Endpoints

| Endpoint | Description |
|----------|-------------|
| `POST /api/tasks` | Create a new task. |
| `PUT /api/tasks/{id}/status` | Update the status of a task. |
| `PUT /api/tasks/{id}/reassign` | Reassign a task to a different user. |
| `POST /api/tasks/{id}/comments` | Add a comment to a task. |
| `POST /api/tasks/{id}/attachments` | Attach a file to a task. |
| `GET /api/tasks/{id}` | View task details. |
| `GET /api/tasks` | View all tasks with advanced filters (e.g., type, status, assigned_to, etc.). |
| `POST /api/tasks/{id}/assign` | Assign a task to a user. |
| `GET /api/reports/daily-tasks` | Generate a daily task report. |
| `GET /api/tasks?status=Blocked` | View delayed tasks blocked due to dependencies. |

---

### Security & Protection

1. **JWT Authentication**
   - Uses JSON Web Token (JWT) for secure API authentication, preventing unauthorized access.

2. **Rate Limiting**
   - Implements request rate limiting to protect against DDoS attacks.

3. **CSRF Protection**
   - Ensures API is protected from Cross-Site Request Forgery (CSRF) attacks.

4. **XSS and SQL Injection Protection**
   - Utilizes Laravel's built-in protections to sanitize user data and prevent XSS and SQL injection attacks.

5. **Permission-based Authorization**
   - Uses Role model for managing user permissions, allowing only authorized users to perform specific actions (e.g., task assignment, status updates).

---

### Advanced Features

1. **Task Dependencies**
   - Uses a `task_dependencies` table to store task dependencies. Tasks are automatically set to `Blocked` if they depend on an incomplete task.

2. **Automatic Reassignment**
   - Upon completion of a task with dependencies, any dependent tasks are automatically unblocked if all conditions are met.

3. **Performance Analysis & Task Management**
   - Uses Job Queues for improved system performance, especially when handling a large number of tasks or users (e.g., scheduling daily performance reports in the background).

4. **Error Handling**
   - Implements error logging to capture and store information on API errors for later analysis.

---

### Attachment Management

1. **Secure File Handling**
   - Ensures secure storage and encryption of attachments on the server.

2. **File Validation**
   - Implements file validation (e.g., virus scanning with external services if available).

---

### Database Performance Optimization

1. **Caching**
   - Caches frequently queried tasks to enhance response times.

2. **Database Indexing**
   - Uses database indexing to speed up search and filter queries.

---

### Error Reporting and Management

1. **Custom Exception Handling**
   - Provides clear error messages for users and stores error information for system administrators.

2. **Error Logging**
   - Stores errors in dedicated tables for analysis, improving application performance over time.

---

### Additional Features

1. **Report Types**
   - Provides an API interface for generating various report types (e.g., completed tasks, delayed tasks, tasks by user) with advanced filtering options.

2. **Soft Delete with Recovery**
   - Supports soft deletion and recovery of tasks, maintaining historical data.

---
Routes Configuration
To use the routes for the API endpoints, add the following code to your api.php routes file:

php
Copy code
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Api\ReportController;

Route::post('/login', [AuthController::class, 'login']);

Route::group([
    'middleware' => ['auth:api', 'throttle:60,1','security_middleware']
], function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/me', [AuthController::class, 'info']);

    // User Management Routes
    Route::apiResource('/user', UserController::class);
    Route::post('/user/restore/{id}', [UserController::class, 'restore']);
    Route::delete('/user/force/{id}', [UserController::class, 'forceDelete']);

    // Task Routes
    Route::apiResource('/tasks', TaskController::class);
    Route::put('/tasks/{task}/status', [TaskController::class, 'updateStatus']);
    Route::post('/tasks/{task}/assign', [TaskController::class, 'assignTask']);
    Route::put('/tasks/{task}/reAssign', [TaskController::class, 'reAssignTask']);
    Route::post('/tasks/{task}/attachments', [TaskController::class, 'uploadAttachment']);
    Route::post('/tasks/{task}/comments', [TaskController::class, 'addComment']);
    Route::get('/reports/daily-tasks', [TaskController::class, 'taskReports']);

    // Role Routes
    Route::apiResource('/role', RoleController::class);

    // Report Routes
    Route::post('/complete-report', [ReportController::class, 'create_Complete_Task_Report']);
    Route::post('/Uncomplete-report', [ReportController::class, 'create_UnCompleted_Task_Report']);
    Route::get('/filter-complete-report', [ReportController::class, 'getReportsByfilters']);
});

### Testing
For testing the API, you can use both unit and feature tests in Laravel:

Setup PHPUnit:

In your project root, set up your .env.testing file with database configurations for testing.
Run migrations: php artisan migrate --env=testing.
Writing Tests:

Unit Tests: Test individual model and service functionality.
Feature Tests: Test the routes and endpoints to ensure they are functioning correctly.
Example Feature Test:

Here is a basic example for testing the task creation endpoint:

public function testCreateTask()
{
    $response = $this->actingAs($user, 'api')
        ->postJson('/api/tasks', [
            'title' => 'New Task',
            'description' => 'Description for the task',
            'type' => 'Feature',
            'priority' => 'High',
        ]);

    $response->assertStatus(201)
        ->assertJson([
            'data' => [
                'title' => 'New Task',
            ],
        ]);
}
Running Tests:

Run php artisan test to execute all tests and validate API functionality.
Testing with Postman:

Import the Postman collection provided.
Configure environment variables for JWT and other settings.
Verify each endpoint, paying attention to permissions and responses.


## API Documentation

- Comprehensive API documentation is available via Postman, including sample requests and responses for each endpoint. It also details authentication requirements, possible error messages, and advanced filter usage.

https://documenter.getpostman.com/view/34411360/2sAXxWYToW
