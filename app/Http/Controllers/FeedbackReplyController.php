<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class FeedbackReplyController extends Controller
{
    public function send(Request $request, $id)
    {
        $request->validate([
            'reply_message' => 'required|string',
        ]);
        $contact = ContactMessage::findOrFail($id);
        $to = $contact->email;
        $subject = 'Reply to your feedback: ' . $contact->subject;
        $body = $request->input('reply_message');

        Mail::raw($body, function ($message) use ($to, $subject) {
            $message->to($to)
                ->subject($subject);
        });

        return response()->json(['success' => true]);
    }
}
