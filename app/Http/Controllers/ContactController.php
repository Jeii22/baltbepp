<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactFormMail;
use App\Models\ContactMessage;

class ContactController extends Controller
{
    public function submit(Request $request)
    {
        // Validate the form data
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        try {
            // Save to database
            ContactMessage::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'subject' => $validated['subject'],
                'message' => $validated['message'],
            ]);

            // Prepare the data for email
            $contactData = [
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'subject' => $validated['subject'],
                'message' => $validated['message'],
            ];

            // Send email to baltbep@gmail.com
            Mail::to('baltbep@gmail.com')->send(new ContactFormMail($contactData));

            return back()->with('contact_success', true);
        } catch (\Exception $e) {
            return back()->with('contact_error', 'Failed to send message. Please try again later.');
        }
    }

    public function markAsRead($id)
    {
        $message = ContactMessage::findOrFail($id);
        $message->update(['is_read' => true]);

        return back()->with('success', 'Message marked as read.');
    }
}
