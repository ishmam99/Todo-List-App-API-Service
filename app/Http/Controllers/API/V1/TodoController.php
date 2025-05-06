<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\TodoResource;
use App\Models\Todo;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    /**
         * Get all tasks
         */
        public function index()
        {
        $todos =auth()->user()->todos;
            return response()->json(TodoResource::collection($todos));
        }

        /**
         * Create a new task
         */
        public function store(Request $request)
        {
            $request->validate([
            'label' => 'required|string|max:255',
            'description' => 'required|string'
            ]);

            $task = auth()->user()->todos()->create([
            'label' => $request->label,
            'description' => $request->description,
                'completed' => false
            ]);

            return response()->json(TodoResource::make($task), 201);
        }

        /**
         * Update a task
         */
        public function update(Request $request, $id)
        {
         
            $request->validate([
            'label' => 'required|string|max:255',
            'description' => 'required|string'
            ]);

            $task = Todo::findOrFail($id);
            $task->update($request->all());

            return response()->json([TodoResource::make($task)],200);
        }

        /**
         * Delete a task
         */
        public function destroy($id)
        {
            $task = Todo::findOrFail($id);
            $task->delete();

            return response()->json(null, 204);
        }

        /**
         * Toggle task completion status
         */
        public function toggleComplete(Todo $todo)
        {
        $todo->is_completed = !$todo->is_completed;
        $todo->completed_at = $todo->is_completed ? now() : null;
        $todo->save();

            return response()->json([TodoResource::make($todo)], 200);
        }

}
