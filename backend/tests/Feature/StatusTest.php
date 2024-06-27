<?php

namespace Tests\Feature;

use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use Illuminate\Support\Str;
use Tests\Feature\factory;

class StatusTest extends TestCase
{
    private function login()
    {
        if ($this->isAuthenticated()) return $this->isAuthenticated();

        //login the user
        $this->postJson('/api/login', [
            'email' => 'admin@projectcode.ug',
            'password' => 'P@ssw0rd',
        ]);
    }

    private function createRecord()
    {
        return $this->postJson('/api/statuses', [
            'name' => "Test record",
            'color' => "bg-purple-600",
            'description' => fake()->text(200)
        ]);
    }

    private function deleteRecord($id)
    {
        return $this->deleteJson('/api/statuses/' . $id);
    }

    public function test_user_can_create_status()
    {
        $this->login();

        $this->assertAuthenticated();

        //create the record
        $response = $this->createRecord();

        $response->assertStatus(200);

        $response->assertJson(
            fn (AssertableJson $json) => $json->where('status', 'success')->etc()
        );

        // delete the record
        $response = $this->deleteRecord($response["data"]["id"]);
        $response->assertStatus(200);

        $response->assertJson(
            fn (AssertableJson $json) => $json->where('status', 'success')->etc()
        );
    }

    public function test_user_can_delete_status()
    {
        $this->login();

        $this->assertAuthenticated();

        $response = $this->createRecord();

        $response->assertStatus(200);

        $response->assertJson(
            fn (AssertableJson $json) => $json->where('status', 'success')->etc()
        );

        $this->patchJson('/api/statuses/' . $response["data"]["id"], [
            'name' => "Updated - Test record",
            'color' => "bg-purple-600",
            'description' => fake()->text(200)
        ]);

        $response->assertStatus(200);

        $response->assertJson(
            fn (AssertableJson $json) => $json->where('status', 'success')->etc()
        );

        // delete the record
        $response = $this->deleteRecord($response["data"]["id"]);
        $response->assertStatus(200);

        $response->assertJson(
            fn (AssertableJson $json) => $json->where('status', 'success')->etc()
        );
    }
}
