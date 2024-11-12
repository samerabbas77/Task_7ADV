<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class UserTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_the_index_get_all_user(): void
    {
        $user = User::find(15);

        $response = $this->actingAs($user,'api')->getJson('/api/user');

        $response->assertStatus(200)->assertJsonStructure(
            [
                "status",
                "message",
                "data"
            ]
            );
        
    }
    //..................................................................................
    //..................................................................................
    public function test_show_get_single_user(): void
    {
        $user = User::find(15);

        $response = $this->actingAs($user,'api')->getJson('/api/user/5');

        $response->assertStatus(200)->assertJsonStructure(
            [
                "status",
                "message",
                "data"
            ]
            );
    }
     //..................................................................................
    //..................................................................................
    public function test_store_create_user(): void
    {
        $user = User::find(15);
        $response = $this->actingAs($user,'api')->postJson('/api/user',[
            "firstName"=>"raghad",
            "lastName"=>"ahmed",
            "email"=>"raghad@example.com",
            "password"=>"password",
            "password_confirmation" => 'password',
            "role" => 'user'
        ]);
        $response->assertStatus(200)->assertJsonStructure(
            [
                "status",
                "message",
                "data"
            ]
            );
    }
    //..................................................................................
    //..................................................................................

    public function test_update_update_user(): void
    {
        $user = User::find(15);
        $response = $this->actingAs($user,'api')->putJson('/api/user/17',[
            "firstName"=>"raghad",
            "lastName"=>"ahmed",
            "email"=>"",
            "password"=>"",
            "password_confirmation" => '',
            "role" => ''
            
        ]);
       
        $response->assertStatus(200)->assertJsonStructure(
            [
                "status",
                "message",
                "data"
            ]
            );
    }
    //..................................................................................
    //..................................................................................
    public function test_delete_delete_user(): void
    {
        $user = User::find(15);
        $response = $this->actingAs($user,'api')->deleteJson('/api/user/1');
        $response->assertStatus(200)->assertJsonStructure(
            [
                "status",
                "message",
                "data"
            ]
            );
    }


    //..................................................................................
    //..................................................................................
    public function test_the_get_Users_With_AssignedTasks()
    {
       $user = User::find(15);
       
       $response = $this->actingAs($user,'api')->getJson('/api/Users/assigned-tasks',
       [
        "per_page" => '5',
        'status' => '',
        'pririty'=> ''
       ]);

       $response->assertStatus(200)->assertJsonStructure(
            [
                "status",
                "message",
                "data"
            ]
            );

    }

    //...................................................................................
    //...................................................................................
    public function test_the_trashed_methode()
    {
        $user = User::find(15);
        $response = $this->actingAs($user,'api')->getJson('/api/user-trash');
        $response->assertStatus(200)->assertJsonStructure(
            [
                "status",
                "message",
                "data"
            ]
        );
    }

    public function test_the_restore_methode()
    {
        $user = User::find(15);
        $response = $this->actingAs($user,'api')->postJson('/api/user/restore/1');
        $response->assertStatus(200)->assertJsonStructure(
            [
                "status",
                "message",
                "data"
            ]
        );
    }

    public function test_the_forceDelete_methode()
    {
        $user = User::find(15);
        $response = $this->actingAs($user,'api')->deleteJson('/api/user/force/1');
        $response->assertStatus(200)->assertJsonStructure(
            [
                "status",
                "message",
                "data"
            ]
        );
    }

}
