<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Models\User;
use App\Notifications\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class ContactController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(ContactRequest $request)
    {
        $data = $request->only(['name', 'email', 'project_call', 'project', 'message']);
        $data['visitor'] = !Auth::check();
        Notification::send(User::role(['administrator', 'manager'])->get(), new ContactMessage($data));

        return redirect()->back()
            ->with('success', __('pages.contact.contact_sent'));
    }
}
