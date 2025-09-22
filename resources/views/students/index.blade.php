<!DOCTYPE html>
<html>
<head>
    <title>Students Management</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .invalid-feedback { color: red; display: none; }
        .is-invalid { border: 1px solid red; }
        table { border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 8px 12px; border: 1px solid #333; }
    </style>
</head>
<body>

    <h1>Add Student</h1>

    <!-- Response message -->
    <div id="responseMessage"></div>

    <!-- Student Form -->
    <form id="ajaxStudentForm" autocomplete="off">
        @csrf
        <div>
            <label>Full Name</label><br>
            <input type="text" name="name" id="name" required>
            <div class="invalid-feedback"></div>
        </div>

        <div>
            <label>Email Address</label><br>
            <input type="email" name="email" id="email" required>
            <div class="invalid-feedback"></div>
        </div>

        <button type="submit">Add Student</button>
    </form>

    <hr>

    <!-- Students List -->
    <h2>Students List</h2>
    <table width="60%">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Verified</th>
            </tr>
        </thead>
        <tbody id="studentsTable">
            <!-- Data will be loaded via AJAX -->
        </tbody>
    </table>

<script>
$(document).ready(function() {

    // CSRF setup for all AJAX requests
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    // Function to fetch students
    function fetchStudents() {
        $.ajax({
            url: "{{ route('students.fetch') }}",
            type: "GET",
            success: function(response) {
                if(response.success) {
                    let rows = '';
                    response.students.forEach(function(student) {
                        rows += `
                            <tr>
                                <td>${student.id}</td>
                                <td>${student.name}</td>
                                <td>${student.email}</td>
                                <td>${student.is_verified ? 'Yes' : 'No'}</td>
                            </tr>
                        `;
                    });
                    $('#studentsTable').html(rows);
                }
            },
            error: function(xhr) {
                console.log('Error fetching students:', xhr);
            }
        });
    }

    // Initially fetch students
    fetchStudents();

    // Handle form submission
    $('#ajaxStudentForm').submit(function(e) {
        e.preventDefault();

        // Clear previous errors
        $('.invalid-feedback').hide().text('');
        $('input').removeClass('is-invalid');

        $.ajax({
            url: "{{ route('students.storeAjax') }}",
            type: "POST",
            data: {
                name: $('#name').val(),
                email: $('#email').val(),
            },
            success: function(response) {
                $('#responseMessage').html('<p style="color:green;">' + response.message + '</p>');
                $('#ajaxStudentForm')[0].reset();
                fetchStudents(); // Refresh list
            },
            error: function(xhr) {
                if(xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    if(errors.name) {
                        $('#name').addClass('is-invalid');
                        $('#name').next('.invalid-feedback').text(errors.name[0]).show();
                    }
                    if(errors.email) {
                        $('#email').addClass('is-invalid');
                        $('#email').next('.invalid-feedback').text(errors.email[0]).show();
                    }
                } else {
                    $('#responseMessage').html('<p style="color:red;">Server error: ' + xhr.status + '</p>');
                }
            }
        });

    });

});
</script>

</body>
</html>
