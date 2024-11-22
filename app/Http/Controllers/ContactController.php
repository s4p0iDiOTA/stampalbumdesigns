<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function index()
    {
        return view('contact');
    }

    public function submit(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string',
        ]);

        // Handle form submission, e.g., send an email
        Mail::raw($validated['message'], function($message) use ($validated) {
            $message->to('admin@example.com')
                ->subject('New Contact Form Submission')
                ->from($validated['email'], $validated['name']);
        });

        return redirect()->route('contact.index')->with('success', 'Message sent successfully!');
    }
}
