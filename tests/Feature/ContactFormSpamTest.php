<?php

namespace Tests\Feature;

use App\Filament\Resources\ContactMessageResource\Pages\ManageContactMessages;
use App\Http\Livewire\Sytatsu\Components\ContactForm;
use App\Http\Livewire\Sytatsu\Components\MaintenanceRepairForm;
use App\Http\Livewire\Sytatsu\Components\PrintRequestForm;
use App\Mail\Sytatsu\Contact\Confirmation;
use App\Models\ContactMessage;
use App\Support\ContactSpamDetector;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Lunar\Admin\Models\Staff;
use Tests\TestCase;

class ContactFormSpamTest extends TestCase
{
    use RefreshDatabase;

    private const SPAM_DETAILS = 'ePMhllnugtxYbVBCNMabkhQDBCjyKtrQxffkEBDey';

    private const REAL_DETAILS = 'Graag wil ik vragen naar mogelijkheden voor het ontwikkelen van een website.';

    public function test_spam_is_stored_without_sending_mail_but_shows_success(): void
    {
        Mail::fake();

        Livewire::test(ContactForm::class)
            ->set('name', 'TRaPfxzYmyYnqYlajl')
            ->set('email', 'j.le.wi.s4.2@gmail.com')
            ->set('details', self::SPAM_DETAILS)
            ->call('send')
            ->assertHasNoErrors()
            ->assertSet('hasBeenSend', true);

        Mail::assertNothingSent();
        $this->assertTrue(ContactMessage::sole()->is_spam);
    }

    public function test_real_contact_message_is_stored_and_mailed(): void
    {
        Mail::fake();

        Livewire::test(ContactForm::class)
            ->set('name', 'Steve')
            ->set('email', 'steve@sytatsu.nl')
            ->set('details', self::REAL_DETAILS)
            ->call('send')
            ->assertSet('hasBeenSend', true);

        Mail::assertSent(Confirmation::class);
        $message = ContactMessage::sole();
        $this->assertFalse($message->is_spam);
        $this->assertSame('contact', $message->service_type);
    }

    public function test_print_and_maintenance_forms_are_protected_too(): void
    {
        Mail::fake();

        Livewire::test(PrintRequestForm::class)
            ->set('name', 'WJGWHLwBcydNRWnGDfW')
            ->set('email', 'lorraine@leases.net')
            ->set('details', self::SPAM_DETAILS)
            ->call('send')
            ->assertSet('hasBeenSend', true);

        Livewire::test(MaintenanceRepairForm::class, ['service_type' => 'repair'])
            ->set('name', 'Steve')
            ->set('email', 'steve@sytatsu.nl')
            ->set('details', self::REAL_DETAILS)
            ->call('send')
            ->assertSet('hasBeenSend', true);

        Mail::assertSentCount(1);
        $this->assertTrue(ContactMessage::where('service_type', 'custom_print')->sole()->is_spam);
        $this->assertFalse(ContactMessage::where('service_type', 'repair')->sole()->is_spam);
    }

    public function test_detector_does_not_flag_normal_submissions(): void
    {
        $detector = new ContactSpamDetector();

        $this->assertFalse($detector->isSpam('Steve', 'steve@example.com', 'Hello there'));
        $this->assertFalse($detector->isSpam('JanVanDerBerg', 'jan@example.nl', 'Interested in working together.'));
        $this->assertFalse($detector->isSpam('Steve', 'steve@sytatsu.nl', self::REAL_DETAILS));
    }

    public function test_staff_can_review_and_unflag_messages(): void
    {
        $staff = Staff::factory()->create(['admin' => true]);
        $spam = ContactMessage::create([
            'name' => 'TRaPfxzYmyYnqYlajl',
            'email' => 'j.le.wi.s4.2@gmail.com',
            'details' => self::SPAM_DETAILS,
            'is_spam' => true,
            'spam_reason' => 'random message',
        ]);

        $this->actingAs($staff, 'staff')->get('/lunar/contact-messages')->assertOk();

        Livewire::test(ManageContactMessages::class)
            ->filterTable('is_spam', true)
            ->assertCanSeeTableRecords([$spam])
            ->callTableAction('markNotSpam', $spam);

        $this->assertFalse($spam->refresh()->is_spam);
    }
}
