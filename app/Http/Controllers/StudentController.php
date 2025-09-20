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

public function create()
{
    $students = Student::all();
    return view('students.create', compact('students'));
}
public function fetch()
{
    $students = Student::all();

    // Return JSON for AJAX
    return response()->json([
        'success'  => true,
        'students' => $students
    ]);
}


public function storeAjax(Request $request)
{
    $request->validate([
        'name'  => 'required',
        'email' => 'required|email|unique:students,email',
    ]);

    $student = Student::create([
        'name'              => $request->name,
        'email'             => $request->email,
        'is_verified'       => 0,
        'verification_token'=> \Str::random(40),
    ]);

    // Optional: send verification mail (wrapped in try/catch)
    try {
        $verificationLink = route('students.verify', ['token' => $student->verification_token]);
        Mail::raw("Hi {$student->name}, click here to verify: $verificationLink", function ($message) use ($student) {
            $message->to($student->email)
                    ->subject('Verify your Student Account');
        });
    } catch (\Exception $e) {
        \Log::error('Mail sending failed: '.$e->getMessage());
    }

    return response()->json([
        'success' => true,
        'message' => 'Student added! Verification email sent.',
        'student' => $student
    ]);
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
