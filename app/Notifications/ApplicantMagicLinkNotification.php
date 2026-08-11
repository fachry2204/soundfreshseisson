<?php

namespace App\Notifications;

use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class ApplicantMagicLinkNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(public Submission $submission) {}

    public function backoff(): array
    {
        return [10, 60, 300];
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = URL::temporarySignedRoute('applicant.portal', now()->addMinutes(30), ['submission' => $this->submission->id]);

        return (new MailMessage)
            ->subject('Akses pendaftaran '.$this->submission->registration_number)
            ->greeting('Halo '.$this->submission->applicant->full_name.',')
            ->line('Gunakan tautan aman berikut untuk melihat status pendaftaran Original Sessions kamu.')
            ->action('Buka Portal Pendaftar', $url)
            ->line('Tautan berlaku selama 30 menit dan hanya dikirim ke email terdaftar.');
    }
}
