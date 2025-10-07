<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Area;
use App\Models\PenanggungJawab;
use App\Models\Laporan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
// Mailables may be absent in this build; we will guard usages at runtime
use Carbon\Carbon;

trait HandlesLaporan
{
    private function applyFilters(Request $request, $query)
    {
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        if ($request->filled('area_id')) {
            $query->where('area_id', $request->area_id);
        }

        if ($request->filled('penanggung_jawab_id')) {
            $query->where('penanggung_jawab_id', $request->penanggung_jawab_id);
        }

        if ($request->filled('problem_category_id')) {
            $query->where('problem_category_id', $request->problem_category_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('tenggat_bulan')) {
            $month = $request->tenggat_bulan;
            $query->whereMonth('tenggat_waktu', $month);
        }

        return $query;
    }

    private function sendSupervisorNotifications($laporan)
    {
        try {
            $laporan = Laporan::with(['area', 'penanggungJawab'])->find($laporan->id);
            $recipients = [];

            if ($laporan->penanggungJawab && $laporan->penanggungJawab->email) {
                // prioritize station PIC email
                $recipients[] = $laporan->penanggungJawab->email;
            } elseif ($laporan->area) {
                foreach ($laporan->area->penanggungJawabs as $pj) {
                    if ($pj->email) {
                        $recipients[] = $pj->email;
                    }
                }
            }

            $recipients = array_unique($recipients);
            foreach ($recipients as $email) {
                if (class_exists(\App\Mail\LaporanDitugaskanSupervisor::class)) {
                    Mail::to($email)->send(new \App\Mail\LaporanDitugaskanSupervisor($laporan));
                } else {
                    Log::warning('LaporanDitugaskanSupervisor mailable not found; skipping email to ' . $email);
                }
            }
        } catch (\Exception $e) {
            Log::error("Error sending notification email: " . $e->getMessage());
        }
    }

    private function detectChanges(array $oldData, array $newData, string $oldArea, string $oldPenanggungJawab): array
    {
        $perubahan = [];

        $fieldNames = [
            'problem_category_id' => 'Problem Category',
            'deskripsi_masalah' => 'Deskripsi Masalah',
            'tenggat_waktu' => 'Tenggat Waktu',
        ];

        if ($oldData['area_id'] != $newData['area_id']) {
            $newArea = Area::find($newData['area_id'])->name ?? '-';
            $perubahan['Area'] = [
                'old' => $oldArea,
                'new' => $newArea
            ];
        }

        if ($oldData['penanggung_jawab_id'] != $newData['penanggung_jawab_id']) {
            $newPJ = PenanggungJawab::find($newData['penanggung_jawab_id'])->name ?? '-';
            $perubahan['Penanggung Jawab'] = [
                'old' => $oldPenanggungJawab,
                'new' => $newPJ
            ];
        }

        foreach (['problem_category_id', 'deskripsi_masalah'] as $field) {
            if ($oldData[$field] != $newData[$field]) {
                $perubahan[$fieldNames[$field]] = [
                    'old' => $oldData[$field],
                    'new' => $newData[$field]
                ];
            }
        }

        if ($oldData['tenggat_waktu'] != $newData['tenggat_waktu']) {
            $perubahan[$fieldNames['tenggat_waktu']] = [
                'old' => Carbon::parse($oldData['tenggat_waktu'])->format('d/m/Y'),
                'new' => Carbon::parse($newData['tenggat_waktu'])->format('d/m/Y')
            ];
        }

        return $perubahan;
    }

    private function sendEditNotifications($laporan, array $perubahan)
    {
        try {
            $recipients = [];

            if ($laporan->penanggungJawab && $laporan->penanggungJawab->email) {
                $recipients[] = $laporan->penanggungJawab->email;
            } elseif ($laporan->area) {
                foreach ($laporan->area->penanggungJawabs as $pj) {
                    if ($pj->email) {
                        $recipients[] = $pj->email;
                    }
                }
            }

            $recipients = array_unique($recipients);
            foreach ($recipients as $email) {
                if (class_exists(\App\Mail\LaporanDieditSupervisor::class)) {
                    Mail::to($email)->send(new \App\Mail\LaporanDieditSupervisor($laporan, $perubahan));
                } else {
                    Log::warning('LaporanDieditSupervisor mailable not found; skipping email to ' . $email);
                }
            }
        } catch (\Exception $e) {
            Log::error("Error sending edit notification email: " . $e->getMessage());
        }
    }
}


