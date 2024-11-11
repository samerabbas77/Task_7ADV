<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class AuthTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_Login_methode(): void
    {
        
        $response = $this->postJson('api/login',[
            'email' => 'admin@gmail.com',
            'password' => '12345678',
        ]);
       
        $response->assertStatus(200)
                 ->assertJsonStructure(
            [
                "access_token",
                "token_type",
                "expires_in"
            ]
        );
        
    }

    public function test_the_me_methode()
    {
        $user = User::find(15);
        $response = $this->actingAs($user,'api')->getJson('api/me');

        $response->assertStatus(200)->assertJsonFragment(
            [
                "id" => $user->id,
                "firstName" => $user->firstName,
                "lastName" => $user->lastName,
                "email" => $user->email,
                "role" => $user->role,
                "full_name" => $user->full_name,
            ]
            );
    }

    public function test_the_refresh_methode()
    {
        $user = User::find(15);
        //here to test the fresh i need first to have pld token so firs i log in tgen test the route
        auth('api')->login($user);

        $response = $this->actingAs($user,'api')->postJson('api/refresh');
         
        $response->assertStatus(200)->assertJsonStructure(
            [
                "access_token",
                "token_type",
                "expires_in"
            ]
            );
    }

    public function test_the_logout_methode()
    {
        $user = User::find(15);

        //also here i need to be loged in to test the logout route
        auth('api')->login($user);

        $response = $this->actingAs($user,'api')->postJson('api/logout');

        $response->assertStatus(200)->assertJsonFragment(
            [
                "message" => "Successfully logged out"
            ]
            );
    }

    
}
