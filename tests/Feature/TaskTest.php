<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use function PHPSTORM_META\map;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\WithFaker;

use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskTest extends TestCase
{
    /**
     * task index test
     */
    public function test_the_Task_index_methode(): void
    {
        $user = User::find(15);

        $response = $this->actingAs($user,'api')->getJson('api/tasks');

        $response->assertStatus(200)->assertJsonFragment(
            [
                "status"=> "success",
                "message"=> "Get all tasks Successfully",
                
            ]
        ); 
    }
//.............................................................................................
//.............................................................................................

    public function test_the_Task_getAllTaskswithFilters_methode(): void
    {
        $user = User::find(15);

        $response = $this->actingAs($user,'api')->getJson('api/tasks-filter');

        $response->assertStatus(200)->assertJsonFragment(
            [
                "status"=> "success",
                "message"=> "Get all tasks with comments and attachments Successfully",
            ]
            );

    }

    //.............................................................................................
    //.............................................................................................

    public function test_the_Task_getAllBluckedTasks_methode(): void
    {
        $user = User::find(15);

        $response = $this->actingAs($user,'api')->getJson('api/tasks-blocked');

        $response->assertStatus(200)->assertJsonStructure(
            [
                "status",
                "message",
                "data"
            ]
            );

            //Task the data array from the response json array
            $dataArray = $response->json('data');
        
            //check if the response(the result of calling the function tht we tested ) is realy have only Blocked task
        
            foreach($dataArray as $task)
            {
                $this->assertEquals('Blocked',$task['Status']);
            }
            /**
             *array_walk($dataArray, function($task) 
            * {
            *     $this->assertEquals('Blocked', $task['status']);
            * });

             */

    } 

    //.............................................................................................
    //.............................................................................................

    public function test_the_Task_store_methode(): void
    {
        $user = User::find(15);

        $response = $this->actingAs($user,'api')->postJson('api/tasks',[
            'title' => 'test title',
            'description' => 'test description',
            'type' => 'Bug',
            'priority' => 'high',
            "due_date"=> "2024-10-18",
            "task_dependency"=> [1,2,3]
            
        ]);

        $response->assertStatus(200)->assertJsonFragment(
            [
                "status"=> "success",
                "message"=> "Store task Successfully",
            ]
        );

    }
    //..................................................................................................
    //..................................................................................................

    public function test_the_Task_update_methode(): void
    {
        $user = User::find(15);
       
        $response = $this->actingAs($user,'api')->putJson('api/tasks/1',[
            'title' => 'test title updated',
            'description' => 'test description updated',
            'type' => 'Feature',
            'priority' => 'low',
            "due_date"=> "2024-10-20",
           
        ]);

        $response->assertStatus(200)->assertJsonFragment(
            [
                "status"=> "success",
                "message"=> "Update task Successfully",
            ]
        );
    }
    //..................................................................................................
    //..................................................................................................
    public function test_the_Task_show_methode(): void
    {
        $user = User::find(15);
        $task = \App\Models\Task::find(1);

        $response = $this->actingAs($user,'api')->getJson('api/tasks/1');

        $response->assertStatus(200)->assertJsonFragment(
            [
                "status"=> "success",
                "message"=> "Show task Successfully",
                "data" => [
                    "title"=> $task->title,
                    "Description"=> $task->description,
                    "Type"=> $task->type,
                    "Status"=> $task->status,
                    "Priority"=> $task->priority,
                    "Due Date"=> $task->due_date,
                    "Assigned To" => $task->assigned_to ? "{$task->assignedUser->firstName} {$task->assignedUser->lastName}" : "Not Assigned to anyOne ",

                ]
            ]
        );
    }
    //..................................................................................................
    //..................................................................................................
    public function test_the_Task_destroy_methode(): void
    {
        $user = User::find(15);

        $response = $this->actingAs($user,'api')->deleteJson('api/tasks/30');

        $response->assertStatus(200)->assertJsonFragment(
            [
                "status"=> "success",
                "message"=> "Soft Deleting task Successfully",
            ]
        );
    }
    //..................................................................................................
    //..................................................................................................
    
    public function test_the_Task_forceDestroy_methode(): void
    {
        $user = User::find(15);
      
        
        $response = $this->actingAs($user,'api')->deleteJson('api/tasks/30/forceDelete');

        $response->assertStatus(200)->assertJsonFragment(
            [
                "status"=> "success",
                "message"=> "force Deleting task Successfully",
            ]
        );
    }
    
    //..................................................................................................
    //..................................................................................................
    public function test_the_Task_trashed_methode(): void
    {
        $user = User::find(15);

        $response = $this->actingAs($user,'api')->getJson('api/tasks-trash');

        $response->assertStatus(200)->assertJsonFragment(
            [
                "status"=> "success",
                "message"=> "trashed tasks fetching successfully",
            ]
        );
    }
    //..................................................................................................
    //..................................................................................................
    public function test_the_Task_restore_methode(): void
    {
        $user = User::find(15);

        $response = $this->actingAs($user,'api')->getJson('api/tasks/28/restore');

        $response->assertStatus(200)->assertJsonFragment(
            [
                "status"=> "success",
                "message"=> "User restored Successfully",
            ]);
                          
    }
    //................................................................................................
    //................................................................................................

public function test_update_status()
{
    $user = User::find(15);
    $response = $this->actingAs($user, 'api')->putJson('api/tasks/1/status', [
        'status' => 'Completed'
    ]);

    $response->assertStatus(200)->assertJsonFragment([
        "status" => "success",
        "message" => "Update task status Successfully",
    ]);

}
//.....................................................................................................
//.....................................................................................................

public function test_assign_task()
{
    $user = User::find(15);
    $response = $this->actingAs($user, 'api')->postJson('api/tasks/11/assign', [
        'assigned_to' => 'Ara Grady',
         
    ]);

    $response->assertStatus(200)->assertJsonFragment([
        "status" => "success",
        "message" => "Assign task Successfully",
    ]);
}

    public function test_reassign_task()
    {
        $user = User::find(15);
        $response = $this->actingAs($user, 'api')->putJson('api/tasks/1/reAssign', [
            'assigned_to' => 'Leatha Collier',
           
        ]);

        $response->assertStatus(200)->assertJsonFragment([
            "status" => "success",
            "message" => "Assign task Successfully",
        ]);
    }

    // public function test_upload_attachment()
    // {
    //     $user = User::find(15);
    //     $file = UploadedFile::fake()->image('test.jpg');

    //     $response = $this->actingAs($user, 'api')->postJson('api/tasks/1/attachments', [
    //         'file' => $file
    //     ]);

    //     $response->assertStatus(200)->assertJsonFragment([
    //         "status" => "success",
    //         "message" => "Upload fle successfully",
    //     ]);
    // }

    public function test_add_comment()
    {
        $user = User::find(15);
        $response = $this->actingAs($user, 'api')->postJson('api/tasks/1/comments', [
            'comment' => 'This is a test comment.'
        ]);

        $response->assertStatus(200)->assertJsonFragment([
            "status" => "success",
            "message" => "Add comment Successfully",
        ]);
    }

    public function test_task_reports()
    {
        $user = User::find(15);
        $response = $this->actingAs($user, 'api')->getJson('api/reports/daily-tasks');

        $response->assertStatus(200)->assertJsonFragment([
            "status" => "success",
            "message" => "Task reports generated successfully",
        ]);
    }

    
}
