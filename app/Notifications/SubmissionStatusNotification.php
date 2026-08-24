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

    public function __construct(
        public Submission $submission,
        public SubmissionStatus $status,
        public ?string $reason = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        if ($this->status === SubmissionStatus::Submitted) {
            return (new MailMessage)
                ->subject('Lagu berhasil disubmit — '.$this->submission->registration_number)
                ->view('emails.submission-received', [
                    'submission' => $this->submission,
                    'applicant' => $this->submission->applicant,
                    'song' => $this->submission->song,
                    'files' => $this->submission->files,
                    'links' => $this->submission->links->keyBy('type'),
                    'trackingUrl' => route('applicant.request'),
                ]);
        }

        $copy = match ($this->status) {
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
            SubmissionStatus::Submitted => 'Demo kamu sudah kami terima dan akan masuk pemeriksaan administrasi.',
        };

        $statusLabel = match ($this->status) {
            SubmissionStatus::AdministrativeReview => 'Di Review',
            SubmissionStatus::Selected => 'Diterima',
            SubmissionStatus::NotSelected => 'Ditolak',
            default => $this->status->publicLabel(),
        };

        return (new MailMessage)
            ->subject('Status '.$statusLabel.' — '.$this->submission->registration_number)
            ->view('emails.submission-status-updated', [
                'submission' => $this->submission,
                'applicant' => $this->submission->applicant,
                'song' => $this->submission->song,
                'statusLabel' => $statusLabel,
                'statusCopy' => $copy,
                'reason' => filled($this->reason) ? $this->reason : 'Tidak ada catatan tambahan dari tim.',
                'trackingUrl' => route('applicant.request'),
            ]);
    }
}
