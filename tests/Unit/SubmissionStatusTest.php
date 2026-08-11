<?php

namespace Tests\Unit;

use App\Enums\SubmissionStatus;
use PHPUnit\Framework\TestCase;

class SubmissionStatusTest extends TestCase
{
    public function test_internal_statuses_are_safely_mapped_for_applicants(): void
    {
        $this->assertSame('Dalam Proses Seleksi', SubmissionStatus::Curation->publicLabel());
        $this->assertSame('Pendaftaran Diterima', SubmissionStatus::AdministrativeReview->publicLabel());
        $this->assertSame('Belum Terpilih', SubmissionStatus::NotSelected->publicLabel());
    }
}
