<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\ContactMessageRequest;
use App\Http\Resources\ContactMessage as ResourcesContactMessage;
use App\Models\ContactMessage;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;

class ContactMessageController extends Controller
{
    public function index(Request $request)
    {
        $query = ContactMessage::query();

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        return ResourcesContactMessage::collection($query->paginate(10));
    }

    public function store(ContactMessageRequest $request)
    {
        try {
            $contact_message = new ContactMessage();
            $contact_message->email = Str::title($request->email);
            $contact_message->name = $request->name;
            $contact_message->contact_number = $request->contact_number;
            $contact_message->message = $request->message;
            $contact_message->save();

            return response()->json(['message' => 'Your message has been successfully sent.'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
