<?php

use Illuminate\Support\Facades\Route;
 use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\StudentController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/send-test-mail', function () {

    Mail::raw('Hello! This is a test email from Laravel + Postmark.', function ($message) {
        $message->to('bushra@codekernal.com') // yahan apna email daal sakti ho
                ->subject('Test Email from Laravel');
    });

    return "Test email sent successfully!";
});


// Route::get('/students', [StudentController::class, 'index'])->name('students.index');
// Route::get('/students/create', [StudentController::class, 'create'])->name('students.create');
// Route::post('/students', [StudentController::class, 'store'])->name('students.store');

// verification route
// Route::get('/students/verify/{token}', [StudentController::class, 'verify'])->name('students.verify');

// Route::post('/students/store-ajax', [StudentController::class, 'storeAjax'])->name('students.store.ajax');
// AJAX route to fetch students
// Route::get('/students/fetch', [StudentController::class, 'fetch'])->name('students.fetch');
// Route::get('/students', [StudentController::class, 'index'])->name('students.index');
// Route::get('/students/create', [StudentController::class, 'create'])->name('students.create');
// Route::post('/students/store-ajax', [StudentController::class, 'storeAjax'])->name('students.store.ajax');




Route::get('/students', [StudentController::class, 'index'])->name('students.index');
Route::get('/students/fetch', [StudentController::class, 'fetch'])->name('students.fetch');
// Route::post('/students/store-ajax', [StudentController::class, 'storeAjax'])->name('students.storeAjax');

 Route::get('/students/verify/{token}', [StudentController::class, 'verify'])->name('students.verify');
// Route::post('/students/store-ajax', [StudentController::class, 'storeAjax'])->name('students.storeAjax');
Route::post('/students/store-ajax', [StudentController::class, 'storeAjax'])
    ->name('students.storeAjax');

Route::get('/students/store-ajax', function() {
    return redirect()->route('students.index');
});
