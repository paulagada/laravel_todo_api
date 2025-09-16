<?php

namespace App\Http\Controllers;

use App\Mail\AssignCompleteNotice;
use App\Mail\AssignNotice;
use App\Models\assignTodo;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AssignTodoController extends Controller
{
    public function assignTodoToUser(Request $request, Todo $todo) {
        
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response([
                'message' => "User not found",
            ], 400); 
        }

        $assignTodo = AssignTodo::where('user_id', $user->id)
                                ->where('todo_id', $todo->id)->first();
        if ($assignTodo) {
            return response([
                'message' => "User already assigned to todo",
            ], 400);
        }
        $assignerMail = $request->user()->email;
        $userEmail = $user->email;
        $todoTitle = $todo->title;
        $assignData = [
            'user_id' => $user->id,
            'todo_id' => $todo->id,
        ];
        assignTodo::create($assignData);
        Mail::to($request->email)->send(new AssignNotice($userEmail, $assignerMail, $todoTitle, true));

        return response([
            'message' => "Todo Assigned to $userEmail",
        ], 201);   
    }

    public function getTodoAssignUsers(Todo $todo) {
        $assignedTodos = $todo->assignedUsers()->get();

        $emails =[];

        foreach ($assignedTodos as $assignedTodo) {
            $user = User::find($assignedTodo->user_id);
            if($user) {
                $emails[] = [
                    "id" => $assignedTodo->id,
                    "email" => $user->email];
            }
        }
        return response()->json($emails);
    }

    public function getUserAssignTodos(Request $request) {
        $assignedTodos = $request->user()->assignedTodos()->get();

        $todos =[];

        foreach ($assignedTodos as $assignedTodo) {
            $todo = Todo::find($assignedTodo->todo_id);
            $userEmail = User::where('id', $todo->user_id)->first()->email;
            if($todo) {
                $todos[] = [
                    "id"=> $todo->id,
                    "title" =>  $todo->title,
                    "description" => $todo->description,
                    "completed" => $todo->completed,
                    "assigned_by" => $userEmail
                ];
            }
        }
        return response()->json($todos);
    }

    public function deleteAssignedUser(Todo $todo, Request $request) {
        $userId = User::where('email', $request->email)->first()->id;
        $assignTodo = AssignTodo::where('user_id', $userId)
                                ->where('todo_id', $todo->id)->first();
                
        if (!$assignTodo) {
            return response([
                'message' => "not found",
            ], 400); 
        }

        $assignerMail = $request->user()->email;
        $userEmail = $request->email;
        $todoTitle = $todo->title;
        $assignTodo->delete();
        Mail::to($request->email)->send(new AssignNotice($userEmail, $assignerMail, $todoTitle, false));
        // $request->todo()->assignedUsers->create($request->all);
        return response([
            'message' => "$userEmail unassigned",
        ], 201);   
    }

    public function updateCompleteTodo(Todo $todo, Request $request) {
        $userEmail = $request->user()->email;
        $request->validate(['completed' => 'required|boolean']);
        $completed = $request->completed;
        $assignerMail = User::where('id', $todo->user_id)->first()->email;
        $todoTitle = $todo->title;
        $todo->update([
            'completed' => $completed,
        ]);

        Mail::to($assignerMail)->send(new AssignCompleteNotice($userEmail, $assignerMail, $todoTitle, $completed));
        return response([
            'message' => $completed ?  "marked as completed" : "marked as incomplete",
        ], 201);   
    }

}
