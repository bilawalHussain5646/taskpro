@extends('layouts.app')

@section('content')
<h1>Add New Task</h1>
<a href="{{ route('tasks.index') }}" class="btn btn-secondary mb-3">Back</a>

<form action="{{ route('tasks.store') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label for="title" class="form-label">Task Title</label>
        <input type="text" class="form-control" name="title" required>
    </div>
    <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea class="form-control" name="description"></textarea>
    </div>
    <div class="mb-3">
        <label for="due_date" class="form-label">Due Date</label>
        <input type="date" class="form-control" name="due_date">
    </div>
    <button type="submit" class="btn btn-success">Add Task</button>
</form>
@endsection
