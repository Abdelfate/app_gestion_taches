<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index()
    {
        // Récupérer toutes les tâches non supprimées pour les utilisateurs, et toutes les tâches pour les administrateurs
        $tasks = Auth::user()->role === 'admin' ? Task::withTrashed()->get() : Task::where('user_id', Auth::id())->get();
        return response()->json($tasks);
    }

    public function store(Request $request)
    {
        $task = Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->status,
            'due_date' => $request->due_date,
            'user_id' => Auth::id(),
        ]);
        return response()->json($task, 201);
    }

    public function show($id)
    {
        $task = Task::withTrashed()->findOrFail($id);
        if (Auth::user()->role !== 'admin' && $task->user_id !== Auth::id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        return response()->json($task);
    }

    public function update(Request $request, $id)
    {
        $task = Task::withTrashed()->findOrFail($id);
        if (Auth::user()->role !== 'admin' && $task->user_id !== Auth::id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $task->update($request->all());
        return response()->json($task);
    }

    public function destroy($id)
    {
        $task = Task::withTrashed()->findOrFail($id);
        if (Auth::user()->role !== 'admin' && $task->user_id !== Auth::id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $task->delete();
        return response()->json(['message' => 'Task deleted successfully']);
    }

    public function deletedTasks()
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $deletedTasks = Task::onlyTrashed()->get();
        return response()->json($deletedTasks);
    }
}
