<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProgramPeriod;
use App\Models\Submission;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function export(Request $request, ProgramPeriod $period, AuditService $audit): StreamedResponse
    {
        abort_unless(in_array($request->user()->role, ['super_admin', 'admin', 'program_admin'], true), 403);
        $audit->record('report.exported', $period, $request, ['format' => 'csv', 'pii' => false]);
        $filename = 'original-sessions-'.$period->slug.'-'.now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function () use ($period) {
            $out = fopen('php://output', 'wb');
            fputcsv($out, ['Nomor', 'Judul Lagu', 'Genre', 'Kota', 'Provinsi', 'Status', 'Tanggal Submit', 'Skor Agregat']);
            Submission::with(['song', 'applicant'])->where('program_period_id', $period->id)->orderBy('submitted_at')->chunk(200, function ($items) use ($out) {
                foreach ($items as $item) {
                    $aggregate = DB::table('review_scores')->join('review_assignments', 'review_assignments.id', '=', 'review_scores.review_assignment_id')->join('review_criteria', 'review_criteria.id', '=', 'review_scores.review_criteria_id')->where('review_assignments.submission_id', $item->id)->where('review_scores.is_final', true)->selectRaw('round(sum(review_scores.score * review_criteria.weight) / sum(review_criteria.weight), 2) score')->value('score');
                    fputcsv($out, [$item->registration_number, $item->song?->title, $item->song?->genre, $item->applicant?->city, $item->applicant?->province, $item->status->value, $item->submitted_at?->toIso8601String(), $aggregate]);
                }
            });
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8', 'X-Content-Type-Options' => 'nosniff']);
    }
}
