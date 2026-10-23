<?php

namespace App\Http\Livewire\Sytatsu\Concerns;

use App\Mail\Sytatsu\Contact\Confirmation;
use App\Mail\Sytatsu\Contact;
use App\Models\ContactMessage;
use App\Support\ContactSpamDetector;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

trait StoresContactSubmissions
{
    /**
     * Stores the submission and sends the confirmation mail, unless it looks like spam.
     * Spam is stored but not mailed, and the form shows success either way so bots get no signal.
     */
    protected function storeAndSend(array $data): void
    {
        $detector = app(ContactSpamDetector::class);

        $reasons = $detector->isSpam($data['name'], $data['email'], $data['details'])
            ? $detector->reasons($data['name'], $data['email'], $data['details'])
            : [];

        $submission = ContactMessage::create([
            'service_type' => $data['service_type'] ?? 'contact',
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => ($data['phone'] ?? '') ?: null,
            'details' => $data['details'],
            'is_spam' => ! empty($reasons),
            'spam_reason' => $reasons ? implode(', ', $reasons) : null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        if ($submission->is_spam) {
            Log::notice('Contact form spam stored without sending mail', [
                'id' => $submission->id,
                'reason' => $submission->spam_reason,
            ]);
        } else {
            Mail::to($data['email'])->send(new Confirmation(data: $data));
        }

        $this->hasBeenSend = true;
    }
}
