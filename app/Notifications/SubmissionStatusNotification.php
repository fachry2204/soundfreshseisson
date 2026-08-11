<?php

namespace App\Notifications;

use App\Enums\SubmissionStatus;
use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubmissionStatusNotification extends Notification
{
    use Queueable;

    public function __construct(public Submission $submission, public SubmissionStatus $status) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $copy = match ($this->status) {
            SubmissionStatus::Submitted => 'Demo kamu sudah kami terima dan akan masuk pemeriksaan administrasi.',
            SubmissionStatus::AdministrativeReview => 'Data kamu sedang diperiksa oleh tim administrasi.',
            SubmissionStatus::RevisionRequested => 'Tim meminta perbaikan data. Buka portal pendaftar untuk melihat detail dan batas waktunya.',
            SubmissionStatus::Eligible => 'Submission kamu lolos pemeriksaan administrasi.',
            SubmissionStatus::Curation => 'Lagu kamu sedang didengarkan dan dinilai oleh tim kurator.',
            SubmissionStatus::Shortlisted => 'Lagu kamu masuk shortlist Original Sessions.',
            SubmissionStatus::Selected => 'Selamat! Lagu kamu terpilih untuk melanjutkan ke tahap produksi.',
            SubmissionStatus::NotSelected => 'Terima kasih sudah berbagi karya. Kali ini lagu kamu belum terpilih.',
            SubmissionStatus::Withdrawn => 'Pendaftaran kamu telah dibatalkan.',
            SubmissionStatus::Disqualified => 'Pendaftaran dinyatakan tidak memenuhi syarat program.',
            SubmissionStatus::Draft => 'Draft pendaftaran kamu tersimpan.',
        };

        return (new MailMessage)
            ->subject($this->status->publicLabel().' — '.$this->submission->registration_number)
            ->greeting('Halo '.$this->submission->applicant->full_name.',')
            ->line($copy)
            ->line('Nomor pendaftaran: '.$this->submission->registration_number)
            ->action('Lacak Pendaftaran', route('applicant.request'));
    }
}
