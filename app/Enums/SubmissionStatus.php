<?php

namespace App\Enums;

enum SubmissionStatus: string
{
    case Draft = 'draft';
    case Submitted = 'submitted';
    case AdministrativeReview = 'administrative_review';
    case RevisionRequested = 'revision_requested';
    case Eligible = 'eligible';
    case Curation = 'curation';
    case Shortlisted = 'shortlisted';
    case Selected = 'selected';
    case NotSelected = 'not_selected';
    case Withdrawn = 'withdrawn';
    case Disqualified = 'disqualified';

    public function publicLabel(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Submitted, self::AdministrativeReview => 'Pendaftaran Diterima',
            self::RevisionRequested => 'Perlu Perbaikan',
            self::Eligible, self::Curation, self::Shortlisted => 'Dalam Proses Seleksi',
            self::Selected => 'Terpilih',
            self::NotSelected => 'Belum Terpilih',
            self::Withdrawn => 'Dibatalkan',
            self::Disqualified => 'Tidak Memenuhi Syarat',
        };
    }
}
