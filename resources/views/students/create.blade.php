<!DOCTYPE html>
<html>
<head>
    <title>Add Student</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .invalid-feedback { color: red; display: none; }
        .is-invalid { border: 1px solid red; }
    </style>
</head>
<body>
    <h1>Add Student</h1>

    <!-- Response messages -->
    <div id="responseMessage"></div>

    <!-- Student Form -->
    <form id="ajaxStudentForm" autocomplete="off">
        @csrf
        <div>
            <label>Full Name</label><br>
            <input type="text" name="full_name" id="full_name" required>
            <div class="invalid-feedback"></div>
        </div>

        <div>
            <label>Email Address</label><br>
            <input type="email" name="email_address" id="email_address" required>
            <div class="invalid-feedback"></div>
        </div>

        <button type="submit">Add Student</button>
    </form>

    <hr>

    <!-- Student List -->
    <h2>Student List</h2>
    <ul id="ajaxStudentList"></ul>

    <script>
    $(document).ready(function() {

        // Function to fetch students list via AJAX
        function fetchStudentList() {
            $.ajax({
                url: "{{ route('students.fetch') }}",
                type: "GET",
                success: function(response) {
                    $('#ajaxStudentList').empty();
                    $.each(response.students, function(index, student) {
                        $('#ajaxStudentList').append('<li>' + student.name + ' (' + student.email + ')</li>');
                    });
                },
                error: function(xhr) {
                    console.log('Error fetching students:', xhr);
                }
            });
        }

        // Load students initially
        fetchStudentList();

        // Handle form submission
        $('#ajaxStudentForm').submit(function(e) {
            e.preventDefault(); // Important: prevent normal form submission

            // Clear previous errors
            $('.invalid-feedback').hide().text('');
            $('input').removeClass('is-invalid');

            $.ajax({
                url: "{{ route('students.storeAjax') }}", // POST route
                type: "POST",
                data: {
                    name: $('#full_name').val(),
                    email: $('#email_address').val(),
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#responseMessage').html('<p style="color:green;">'+response.message+'</p>');
                    $('#ajaxStudentForm')[0].reset();
                    fetchStudentList(); // Refresh list
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        $.each(errors, function(field, msgs) {
                            if(field === 'name') {
                                $('#full_name').addClass('is-invalid');
                                $('#full_name').next('.invalid-feedback').text(msgs[0]).show();
                            }
                            if(field === 'email') {
                                $('#email_address').addClass('is-invalid');
                                $('#email_address').next('.invalid-feedback').text(msgs[0]).show();
                            }
                        });
                    } else {
                        $('#responseMessage').html('<p style="color:red;">Server error: '+xhr.status+'</p>');
                    }
                }
            });

        });

    });
    </script>
</body>
</html>
