<!DOCTYPE html>
<html>
<head>
    <title>Task Reminder</title>
</head>
<body>
    <p>Hello {{ $user->full_name }},</p>
    <p>You have the following tasks due soon:</p>
    <ul>
        @foreach ($tasks as $task)
            <li>{{ $task->title }} - Due on {{ $task->due_date->format('Y-m-d') }}</li>
        @endforeach
    </ul>
    <p>Please complete these tasks as soon as possible.</p>
</body>
</html>
