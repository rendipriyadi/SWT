<?php

namespace App\Http\Library;

use ErrorException;
use App\Mail\ReportAssignedMail;
use App\Mail\ReportEditedMail;
use App\Mail\ReportCompletedMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

trait SWTEmailNotifications
{
    /**
     * Send email notification when report is assigned to PIC
     * 
     * @param object $laporan
     * @return void
     */
    protected function emailReportAssigned($laporan)
    {
        try {
            // TO: PIC (Person in Charge)
            $to_emails = [];
            if ($laporan->penanggungJawab && $laporan->penanggungJawab->email) {
                $to_emails[] = $laporan->penanggungJawab->email;
            } elseif ($laporan->area) {
                // If no specific PIC, send to all PICs in the area
                foreach ($laporan->area->penanggungJawabs as $pj) {
                    if ($pj->email) {
                        $to_emails[] = $pj->email;
                    }
                }
            }

            // If no recipients, log and return
            if (empty($to_emails)) {
                Log::warning("No email recipients for report ID: {$laporan->id}");
                return;
            }

            // CC: Head/Manager (PICs with station = "General" in the same Area)
            // Exclude the assigned PIC from CC to avoid duplicate
            $cc_emails = [];

            if ($laporan->area) {
                // Get all PICs with station "General" in the same area (case-insensitive)
                foreach ($laporan->area->penanggungJawabs as $pj) {
                    if (strcasecmp($pj->station, 'General') === 0 && $pj->email && $pj->id != $laporan->penanggung_jawab_id) {
                        $cc_emails[] = $pj->email;
                    }
                }
            }

            // Remove duplicates
            $to_emails = array_unique($to_emails);
            $cc_emails = array_unique($cc_emails);

            // Generate encrypted URL using Laravel encrypt
            $encryptedId = encrypt($laporan->id);
            $fullUrl = route('laporan.show', ['id' => $encryptedId]);

            // Send email using Laravel Mailable
            foreach ($to_emails as $email) {
                Mail::to($email)->send(new ReportAssignedMail($laporan, $fullUrl, $encryptedId, $cc_emails));
            }
            
            Log::info("Email sent for report ID: {$laporan->id} to " . implode(', ', $to_emails));
            
        } catch (ErrorException $e) {
            Log::error("Failed to send report assigned email for ID {$laporan->id}: " . $e->getMessage());
        } catch (\Exception $e) {
            Log::error("Unexpected error sending report assigned email for ID {$laporan->id}: " . $e->getMessage());
        }
    }

    /**
     * Send email notification when report is edited
     * 
     * @param object $laporan
     * @param array $perubahan
     * @return void
     */
    protected function emailReportEdited($laporan, array $perubahan)
    {
        try {
            // TO: PIC (Person in Charge)
            $to_emails = [];
            if ($laporan->penanggungJawab && $laporan->penanggungJawab->email) {
                $to_emails[] = $laporan->penanggungJawab->email;
            } elseif ($laporan->area) {
                foreach ($laporan->area->penanggungJawabs as $pj) {
                    if ($pj->email) {
                        $to_emails[] = $pj->email;
                    }
                }
            }

            if (empty($to_emails)) {
                Log::warning("No email recipients for edited report ID: {$laporan->id}");
                return;
            }

            // CC: Head/Manager (PICs with station = "General" in the same Area)
            // Exclude the assigned PIC from CC to avoid duplicate
            $cc_emails = [];

            if ($laporan->area) {
                // Get all PICs with station "General" in the same area (case-insensitive)
                foreach ($laporan->area->penanggungJawabs as $pj) {
                    if (strcasecmp($pj->station, 'General') === 0 && $pj->email && $pj->id != $laporan->penanggung_jawab_id) {
                        $cc_emails[] = $pj->email;
                    }
                }
            }

            // Remove duplicates
            $to_emails = array_unique($to_emails);
            $cc_emails = array_unique($cc_emails);

            // Generate encrypted URL using Laravel encrypt
            $encryptedId = encrypt($laporan->id);
            $fullUrl = route('laporan.show', ['id' => $encryptedId]);

            // Send email using Laravel Mailable
            foreach ($to_emails as $email) {
                Mail::to($email)->send(new ReportEditedMail($laporan, $perubahan, $fullUrl, $encryptedId, $cc_emails));
            }
            
            Log::info("Edit notification email sent for report ID: {$laporan->id}");
            
        } catch (ErrorException $e) {
            Log::error("Failed to send report edited email for ID {$laporan->id}: " . $e->getMessage());
        } catch (\Exception $e) {
            Log::error("Unexpected error sending report edited email for ID {$laporan->id}: " . $e->getMessage());
        }
    }

    /**
     * Send email notification when report is completed
     * 
     * @param object $laporan
     * @return void
     */
    protected function emailReportCompleted($laporan)
    {
        try {
            // TO: PIC (Person in Charge) + Safety coordinators
            $to_emails = [];
            if ($laporan->penanggungJawab && $laporan->penanggungJawab->email) {
                $to_emails[] = $laporan->penanggungJawab->email;
            }

            // CC: Head/Manager (PICs with station = "General" in the same Area)
            // Exclude the assigned PIC from CC to avoid duplicate
            $cc_emails = [];

            if ($laporan->area) {
                // Get all PICs with station "General" in the same area (case-insensitive)
                foreach ($laporan->area->penanggungJawabs as $pj) {
                    if (strcasecmp($pj->station, 'General') === 0 && $pj->email && $pj->id != $laporan->penanggung_jawab_id) {
                        $cc_emails[] = $pj->email;
                    }
                }
            }

            if (empty($to_emails)) {
                Log::warning("No email recipients for completed report ID: {$laporan->id}");
                return;
            }

            // Remove duplicates
            $to_emails = array_unique($to_emails);
            $cc_emails = array_unique($cc_emails);

            // Generate encrypted URL using Laravel encrypt
            $encryptedId = encrypt($laporan->id);
            $fullUrl = route('laporan.show', ['id' => $encryptedId]);

            // Send email using Laravel Mailable
            foreach ($to_emails as $email) {
                Mail::to($email)->send(new ReportCompletedMail($laporan, $fullUrl, $encryptedId, $cc_emails));
            }
            
            Log::info("Completion notification email sent for report ID: {$laporan->id}");
            
        } catch (ErrorException $e) {
            Log::error("Failed to send report completed email for ID {$laporan->id}: " . $e->getMessage());
        } catch (\Exception $e) {
            Log::error("Unexpected error sending report completed email for ID {$laporan->id}: " . $e->getMessage());
        }
    }
}
