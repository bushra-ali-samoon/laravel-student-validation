<!DOCTYPE html>
<html>
<head>
    <title>Students List</title>
</head>
<body>
<h1>Students</h1>

<ul>
    @foreach($students as $student)
        <li>{{ $student->name }} ({{ $student->email }})</li>
    @endforeach
</ul>

<a href="{{ route('students.create') }}">Add New Student</a>
</body>
</html>
