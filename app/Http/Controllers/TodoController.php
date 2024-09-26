<?php

namespace App\Http\Controllers;

use App\Http\Requests\TodoRequest;
use App\Models\Todo;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    use AuthorizesRequests;
    
    public function getTodos(Request $request) {
        $todos = $request->user()->todos()->get();
        return response()->json($todos);
    }

    public function store(TodoRequest $request) {
        $request->validated();
        $todos = $request->user()->todos()->create($request->all());
        return response()->json($todos, 201);
    }

    public function update(TodoRequest $request, Todo $todo) {
        $this->authorize('update', $todo);
        $request->validated();
        $todo->update($request->all());
        return response([
                "message" => "Updated",
                "todo" => $todo
            ]);
    }


    public function destroy(Todo $todo)
    {
        $this->authorize('delete', $todo);

        $todo->delete();

        return response()->json(['message' => 'Todo deleted'], 200);
    }

}
