<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Todo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TodoTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticate()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        return $user;
    }

    public function test_user_can_create_todo()
    {
        $user = $this->authenticate();

        $response = $this->postJson('/api/v1/todos', [
            'label' => 'Test Task',
            'description' => 'This is a test task',
        ]);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'label' => 'Test Task',
                'description' => 'This is a test task',
            ]);
    }

    public function test_user_can_list_todos()
    {
        $user = $this->authenticate();

        $user->todos()->create([
            'label' => 'First Task',
            'description' => 'My first todo',
        ]);

        $response = $this->getJson('/api/v1/todos');

        $response->assertStatus(200)
            ->assertJsonFragment(['label' => 'First Task']);
    }

    public function test_user_can_update_todo()
    {
        $user = $this->authenticate();

        $todo = $user->todos()->create([
            'label' => 'Old Label',
            'description' => 'Old description',
        ]);

        $response = $this->putJson("/api/v1/todos/{$todo->id}", [
            'label' => 'New Label',
            'description' => 'New description',
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['label' => 'New Label']);
    }

    public function test_user_can_delete_todo()
    {
        $user = $this->authenticate();

        $todo = $user->todos()->create([
            'label' => 'Task to delete',
            'description' => 'Some description',
        ]);

        $response = $this->deleteJson("/api/v1/todos/{$todo->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('todos', ['id' => $todo->id]);
    }

    public function test_user_can_toggle_todo_completion()
    {
        $user = $this->authenticate();

        $todo = $user->todos()->create([
            'label' => 'Toggle Task',
            'description' => 'Some desc',
            'is_completed' => false,
        ]);

        $response = $this->getJson("/api/v1/todo-complete/{$todo->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['is_completed' => true]);
    }
}
