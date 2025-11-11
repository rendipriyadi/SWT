<?php

namespace App\Http\Library;

use ErrorException;
use App\Services\MailService;
use App\Mail\ReportEditedMail;
use App\Mail\ReportAssignedMail;
use App\Mail\ReportCompletedMail;
use Illuminate\Support\Facades\Log;

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

            // Remove TO emails from CC to avoid duplicate
            $cc_emails = array_diff($cc_emails, $to_emails);

            // Generate encrypted URL using url() helper
            $encryptedId = encrypt($laporan->id);
            $fullUrl = url('laporan/' . $encryptedId);

            // Send email using Laravel Mailable
            // Send one email with all PICs in TO field
            if (count($to_emails) > 0) {
                MailService::to($to_emails)->send(new ReportAssignedMail($laporan, $fullUrl, $encryptedId, array_values($cc_emails)));
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

            // Remove TO emails from CC to avoid duplicate
            $cc_emails = array_diff($cc_emails, $to_emails);

            // Generate encrypted URL using url() helper
            $encryptedId = encrypt($laporan->id);
            $fullUrl = url('laporan/' . $encryptedId);

            // Send email using Laravel Mailable
            // Send one email with all PICs in TO field
            if (count($to_emails) > 0) {
                MailService::to($to_emails)->send(new ReportEditedMail($laporan, $fullUrl, $encryptedId, $perubahan, array_values($cc_emails)));
            }

            Log::info("Edit notification email sent for report ID: {$laporan->id}");

        } catch (ErrorException $e) {
            Log::error("Error sending report edited email for ID {$laporan->id}: " . $e->getMessage());
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
                Log::warning("No email recipients for completed report ID: {$laporan->id}");
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

            // Remove TO emails from CC to avoid duplicate
            $cc_emails = array_diff($cc_emails, $to_emails);

            // Generate encrypted URL using url() helper
            $encryptedId = encrypt($laporan->id);
            $fullUrl = url('laporan/' . $encryptedId);

            // Send email using Laravel Mailable
            // Send one email with all PICs in TO field
            if (count($to_emails) > 0) {
                MailService::to($to_emails)->send(new ReportCompletedMail($laporan, $fullUrl, $encryptedId, array_values($cc_emails)));
            }

            Log::info("Completion notification email sent for report ID: {$laporan->id}");

        } catch (ErrorException $e) {
            Log::error("Failed to send report completed email for ID {$laporan->id}: " . $e->getMessage());
        } catch (\Exception $e) {
            Log::error("Unexpected error sending report completed email for ID {$laporan->id}: " . $e->getMessage());
        }
    }

    /**
     * Send reminder email for reports grouped by PIC
     *
     * @param \Illuminate\Support\Collection $reports
     * @param \App\Mail\Mailable $mailableClass
     * @return void
     */
    protected function sendReminderByPic($reports, $mailableClass)
    {
        // Group reports by PIC email (or area if no PIC)
        $reportsByPic = $reports->groupBy(function ($report) {
            if ($report->penanggungJawab && $report->penanggungJawab->email) {
                return 'pic_' . $report->penanggungJawab->email;
            } elseif ($report->area) {
                return 'area_' . $report->area->id;
            }
            return 'no_recipient';
        });

        foreach ($reportsByPic as $groupKey => $groupReports) {
            if ($groupKey === 'no_recipient') {
                continue;
            }

            $firstReport = $groupReports->first();

            // TO: PIC (Person in Charge)
            $to_emails = [];
            if ($firstReport->penanggungJawab && $firstReport->penanggungJawab->email) {
                $to_emails[] = $firstReport->penanggungJawab->email;
                $pic = $firstReport->penanggungJawab;
            } elseif ($firstReport->area) {
                foreach ($firstReport->area->penanggungJawabs as $pj) {
                    if ($pj->email) {
                        $to_emails[] = $pj->email;
                    }
                }
                $pic = null;
            }

            if (empty($to_emails)) {
                Log::warning("No email recipients for reminder group: {$groupKey}");
                continue;
            }

            // CC: Head/Manager (PICs with station = "General" in the same Area)
            $cc_emails = [];
            if ($firstReport->area) {
                foreach ($firstReport->area->penanggungJawabs as $pj) {
                    if (strcasecmp($pj->station, 'General') === 0 && $pj->email) {
                        if (!$pic || $pj->id != $pic->id) {
                            $cc_emails[] = $pj->email;
                        }
                    }
                }
            }

            // Remove duplicates
            $to_emails = array_unique($to_emails);
            $cc_emails = array_unique($cc_emails);
            $cc_emails = array_diff($cc_emails, $to_emails);

            // Generate full URL 
            $fullUrl = url('laporan');

            try {
                $mailable = new $mailableClass($groupReports, $pic, $fullUrl, array_values($cc_emails));
                MailService::to($to_emails)->send($mailable);

                Log::info("Reminder email sent to: " . implode(', ', $to_emails) . " ({$groupReports->count()} reports)");
            } catch (\Exception $e) {
                Log::error("Failed to send reminder email to: " . implode(', ', $to_emails) . ". Error: " . $e->getMessage());
            }
        }
    }
}
