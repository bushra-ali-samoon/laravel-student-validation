<!DOCTYPE html>
<html>
<head>
    <title>Add Students</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .invalid-feedback { color: red; display: none; }
    </style>
</head>
<body>
    <h1>Add Student</h1>

    <!-- Response messages -->
    <div id="response"></div>

    <!-- Student Form -->
    <form id="studentForm">
        @csrf
        <div>
            <label>Name</label><br>
            <input type="text" name="name" id="name" required>
            <div class="invalid-feedback"></div>
        </div>

        <div>
            <label>Email</label><br>
            <input type="email" name="email" id="email" required>
            <div class="invalid-feedback"></div>
        </div>

        <button type="submit">Add Student</button>
    </form>

    <hr>

    <!-- Student List -->
    <h2>Add Student</h2>

<form id="studentForm">
    @csrf
    <div>
        <label>Name</label><br>
        <input type="text" name="name" id="name" required>
        <div class="invalid-feedback" style="color:red;display:none;"></div>
    </div>

    <div>
        <label>Email</label><br>
        <input type="email" name="email" id="email" required>
        <div class="invalid-feedback" style="color:red;display:none;"></div>
    </div>

    <button type="submit">Add Student</button>
</form>

<h2>Students List</h2>
<ul id="studentList"></ul>

<div id="response"></div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {

    // Function to fetch students list via AJAX
    function fetchStudents() {
        $.ajax({
            url: "{{ route('students.index.ajax') }}", // new route for fetching students
            type: "GET",
            success: function(response) {
                $('#studentList').empty(); // clear current list
                $.each(response.students, function(index, student) {
                    $('#studentList').append('<li>' + student.name + ' (' + student.email + ')</li>');
                });
            },
            error: function(xhr) {
                console.log('Error fetching students:', xhr);
            }
        });
    }

    // Initially fetch students when page loads
    fetchStudents();

    // Handle student form submission
    $('#studentForm').submit(function(e) {
        e.preventDefault();

        $('.invalid-feedback').hide().text('');
        $('input').removeClass('is-invalid');

        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('input[name="_token"]').val() } });

        $.ajax({
            url: "{{ route('students.store.ajax') }}",
            type: "POST",
            data: $(this).serialize(),
            success: function(response) {
                $('#response').html('<p style="color:green;">'+response.message+'</p>');
                $('#studentForm')[0].reset();
                fetchStudents(); // Refresh the list after adding new student
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    $.each(errors, function(field, msgs) {
                        let $input = $('input[name="'+field+'"]');
                        $input.addClass('is-invalid');
                        $input.next('.invalid-feedback').text(msgs[0]).show();
                    });
                } else {
                    $('#response').html('<p style="color:red;">Server error: '+xhr.status+'</p>');
                }
            }
        });
    });

});
</script>

</body>
</html>
