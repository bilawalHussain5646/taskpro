<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    // Web view
    public function indexView()
    {
        return view('tasks.index');
    }

    // API: Get all tasks for a specific date
    public function index(Request $request)
    {
        // Get date from query param ?date=YYYY-MM-DD
        $date = $request->query('date', date('Y-m-d')); // default today
        $tasks = Task::where('task_date', $date)->get();
        return response()->json($tasks);
    }

    // Store new task
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'due_date' => 'nullable|date',
            'status' => 'required|in:pending,progress,completed',
            'task_date' => 'required|date', // the date this task belongs to
        ]);

        $task = Task::create([
            'title' => $request->title,
            'due_date' => $request->due_date,
            'status' => $request->status,
            'task_date' => $request->task_date,
        ]);

        return response()->json($task);
    }

    // Update task
    public function update(Request $request, Task $task)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'due_date' => 'nullable|date',
            'status' => 'required|in:pending,progress,completed',
            'task_date' => 'required|date',
        ]);

        $task->update([
            'title' => $request->title,
            'due_date' => $request->due_date,
            'status' => $request->status,
            'task_date' => $request->task_date,
        ]);

        return response()->json($task);
    }

    // Delete task
    public function destroy(Task $task)
    {
        $task->delete();
        return response()->json(['message' => 'Task deleted successfully']);
    }
}
