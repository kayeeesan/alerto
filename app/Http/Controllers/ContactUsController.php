<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContactMessage;
use App\Mail\ContactMessageMail;
use Illuminate\Support\Facades\Mail;

class ContactUsController extends Controller
{
    public function store(Request $request)
    {
        // Validate the form data
        $validated = $request->validate([
            'email' => 'required|email',
            'name' => 'required|string',
            'contact_number' => 'required|string',
            'message' => 'required|string',
        ]);

        // Store the message in the database
        $contactMessage = ContactMessage::create($validated);

        // Send the email
        Mail::to('alertoappofficial@gmail.com')->send(new ContactMessageMail($contactMessage));

        // Return a response
        return response()->json(['message' => 'Your message was sent successfully. Thanks for getting in touch!']);
    }
}
