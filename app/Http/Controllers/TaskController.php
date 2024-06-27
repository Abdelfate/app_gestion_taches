<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    // Créer une nouvelle tâche
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $task = Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'user_id' => auth()->id(),
        ]);

        return response()->json($task, 201);
    }

    // Récupérer toutes les tâches
    public function index()
    {
        if (Auth::user()->role === 'admin') {
            $tasks = Task::withTrashed()->get();
        } else {
            $tasks = Task::where('user_id', Auth::id())->get();
        }
        return response()->json($tasks);
    }

    // Récupérer une tâche spécifique
    public function show($id)
    {
        $task = Task::withTrashed()->findOrFail($id);

        if (Auth::user()->role !== 'admin' && $task->user_id !== Auth::id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return response()->json($task);
    }

    // Mettre à jour une tâche
    public function update(Request $request, $id)
    {
        $task = Task::withTrashed()->findOrFail($id);

        if (Auth::user()->role !== 'admin' && $task->user_id !== Auth::id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $task->update($request->only('title', 'description'));

        return response()->json($task);
    }

    // Soft delete d'une tâche
    public function destroy($id)
    {
        $task = Task::findOrFail($id);

        if (Auth::user()->role !== 'admin' && $task->user_id !== Auth::id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $task->delete();

        return response()->json(null, 204);
    }

    // Récupérer les tâches supprimées (uniquement pour les administrateurs)
    public function deletedTasks()
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $tasks = Task::onlyTrashed()->get();
        return response()->json($tasks);
    }
}

