<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ReportTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_the_create_Complete_Task_Report_methode(): void
    {
        $user = \App\Models\User::find(15);
        $response = $this->actingAs($user,'api')->postJson('/api/complete-report');
        $response->assertStatus(200)->assertJsonStructure(
            [
                "status",
                "message",
                "data"
            ]
        );
    }
    public function test_the_create_UnCompleted_Task_Report_methode(): void
    {
        $user = \App\Models\User::find(15);
        $response = $this->actingAs($user,'api')->postJson('/api/Uncomplete-report');
        $response->assertStatus(200)->assertJsonStructure(
            [
                "status",
                "message",
                "data"
            ]
        );
    }
    public function test_the_getReportsByfilters_methode(): void
    {
        $user = \App\Models\User::find(15);
        $response = $this->actingAs($user,'api')->getJson('/api/filter-complete-report',[
            "type"=> "Complete",
            "user"=> 1
        ]);
        $response->assertStatus(200)->assertJsonStructure(
            [
                "status",
                "message",
                "data"
            ]
        );
    }

}
