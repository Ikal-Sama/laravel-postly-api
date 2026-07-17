<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTodoRequest;
use App\Http\Requests\UpdateTodoRequest;
use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return $request->user()->todos()->latest()->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTodoRequest $request)
    {
        $fields = $request->validated();

        $todo = $request->user()->todos()->create($fields);

        return $todo;
    }

    /**
     * Display the specified resource.
     */
    public function show(Todo $todo)
    {
        Gate::authorize('view', $todo); // Automatically throws 403 if unauthorized

        return $todo;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Todo $todo)
    {
        Gate::authorize('update', $todo);

        // 2. Safely cast incoming value to boolean or default to current value
        $todo->update([
            'is_completed' => $request->boolean('is_completed', $todo->is_completed),
            'task'         => $request->input('task', $todo->task), // keeps original text if not passed
        ]);

        return response()->json($todo, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Todo $todo)
    {
        Gate::authorize('delete', $todo); // Automatically throws 403 if unauthorized

        $todo->delete();

        // 3. Return the success response
        return response()->json([
            'message' => 'The task was deleted'
        ], 200);
    }
}
