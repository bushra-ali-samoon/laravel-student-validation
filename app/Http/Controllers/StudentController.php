<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class StudentController extends Controller
{
    // Show all students
    public function index()
    {
        $students = Student::all();
        return view('students.index', compact('students'));
    }

    // Show create form
    public function create()
    {
        return view('students.create');
    }

    // Save new student and send verification email
    public function store(Request $request)
{
    $request->validate([
        'name'  => 'required',
        'email' => 'required|email|unique:students,email',
    ]);

    $student = Student::create([
        'name'              => $request->name,
        'email'             => $request->email,
        'is_verified'       => 0,
        'verification_token'=> Str::random(40),
    ]);

    //  Yahan link generate karna hai
    $verificationLink = route('students.verify', $student->verification_token);

    //  Email bhejna
    Mail::raw("Hi {$student->name}, click here to verify: $verificationLink", function ($message) use ($student) {
        $message->to($student->email)
                ->subject('Verify your Student Account');
    });

    return redirect()->route('students.index')->with('success', 'Student added! Verification email sent.');
}


    // Verify student
    public function verify($token)
    {
        $student = Student::where('verification_token', $token)->first();

        if (!$student) {
            return redirect()->route('students.index')->with('error', 'Invalid verification link.');
        }

        $student->is_verified = 1;
        $student->verification_token = null;
        $student->save();

        return redirect()->route('students.index')->with('success', 'Email verified successfully!');
    }
}
