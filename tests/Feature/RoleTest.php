<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RoleTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_the_Role_index_methode(): void
    {
        $user = User::find(15);
        $response = $this->actingAs($user,'api')->getJson('/api/role');
        $response->assertStatus(200)->assertJsonStructure(
            [
                "status",
                "message",
                "data" => [
                    "current_page",
                    "data" => [
                    "*" => [
                        "id",
                        "name",
                        "guard_name",
                        "created_at",
                        "updated_at"
                    ]
                ],
                "links" => [
                    "*"=> [
                        "url",
                        "label",
                        "active"                
                ]
            ],
                 "next_page_url",
                 "path",
                 "per_page",
                 "prev_page_url",
                 "to",
                 "total"
            ]
                ]
        );
    }
    public function test_the_Role_show_methode(): void
    {
        $user = User::find(15);
        $response = $this->actingAs($user,'api')->getJson('/api/role/1');
        $response->assertStatus(200)->assertJsonStructure(
            [
                "status",
                "message",
                "data" => [
                    "role"=>[
                        "id",
                        "name",
                        "guard_name",
                        "created_at",
                        "updated_at"   
                    ],
                    "rolePermissions"
                ]
            ]
        );
    }
    public function test_the_Role_store_methode(): void
    {
        $user = User::find(15);
        $response = $this->actingAs($user,'api')->postJson('/api/role',[
            "name"=>"test role",
           "permission"=> [1,2,3,9]
        ]);
        $response->assertStatus(200)->assertJsonStructure(
            [
                "status",
                "message",
                "data" => [
                    "guard_name",
                    "name",
                    "created_at",
                    "updated_at",
                    "id",
                ]
            ]
        );
    }
    public function test_the_Role_update_methode(): void
    {
        $user = User::find(15);
        $response = $this->actingas($user,'api')->putJson('/api/role/3',[
            "name"=>"test  updated",
            "permission"=> [1,2,3]
        ]);
        $response->assertStatus(200)->assertJsonStructure(
            [
                "status",
                "message",
                "data" => [
                    "id",
                    "name",
                    "guard_name",
                    "created_at",
                    "updated_at"
                ]
            ]
        );
    }
    public function test_the_Role_destroy_methode(): void
    {
        $response = $this->deleteJson('/api/role/1');
        $response->assertStatus(200)->assertJsonStructure(
            [
                "status",
                "message",
                "data"
            ]
        );
    }
}
