<?php

namespace App\Http\Library;

use ErrorException;
use App\Services\MailService;
use App\Mail\ReportEditedMail;
use App\Mail\ReportAssignedMail;
use App\Mail\ReportCompletedMail;
use App\Models\PenanggungJawab;
use Illuminate\Support\Facades\Log;

trait SWTEmailNotifications
{
    /**
     * Send email notification when report is assigned to PIC
     *
     * @param object $laporan
     * @param array $additionalPics
     * @return void
     */
    protected function emailReportAssigned($laporan, $additionalPics = [])
    {
        try {
            // TO: Main PIC (Person in Charge)
            $to_emails = [];
            $to_names = [];
            $generalPicIds = [];
            
            // First, identify all General PICs in the area
            if ($laporan->area) {
                foreach ($laporan->area->penanggungJawabs as $pj) {
                    if (strcasecmp($pj->station, 'General') === 0) {
                        $generalPicIds[] = $pj->id;
                    }
                }
            }
            
            if ($laporan->penanggungJawab && $laporan->penanggungJawab->email) {
                $to_emails[] = $laporan->penanggungJawab->email;
                $to_names[] = $laporan->penanggungJawab->name;
            } elseif ($laporan->area) {
                // If no specific PIC, send to NON-General PICs in the area
                foreach ($laporan->area->penanggungJawabs as $pj) {
                    if ($pj->email) {
                        // Only add to TO if NOT a General PIC (General goes to CC by default)
                        if (!in_array($pj->id, $generalPicIds)) {
                            $to_emails[] = $pj->email;
                            $to_names[] = $pj->name;
                        }
                    }
                }
            }

            // Add additional PICs to TO emails and names
            $additionalPicNames = [];
            $additionalPics = is_array($additionalPics) ? array_filter($additionalPics) : [];
            
            if (!empty($additionalPics)) {
                $additionalPenanggungJawabs = PenanggungJawab::whereIn('id', $additionalPics)
                    ->select('id', 'name', 'station', 'email')
                    ->get();
                
                foreach ($additionalPenanggungJawabs as $pj) {
                    // Add to names for hello message (regardless of email)
                    $to_names[] = $pj->name;
                    $additionalPicNames[] = $pj->name;
                    
                    // Add to emails only if valid email exists
                    if ($pj->email && filter_var($pj->email, FILTER_VALIDATE_EMAIL)) {
                        $to_emails[] = $pj->email;
                    }
                }
            }

            // If no recipients, log and return
            if (empty($to_emails)) {
                Log::warning("No email recipients for assigned report ID: {$laporan->id}");
                return;
            }

            // CC: Head/Manager (PICs with station = "General" in the same Area)
            // EXCEPT: If General is in Additional PICs, move to TO instead of CC
            $cc_emails = [];

            if ($laporan->area) {
                // Get all PICs with station "General" in the same area (case-insensitive)
                foreach ($laporan->area->penanggungJawabs as $pj) {
                    if (strcasecmp($pj->station, 'General') === 0 && $pj->email) {
                        // Check if this General PIC is in Additional PICs
                        $isInAdditionalPics = !empty($additionalPics) && in_array($pj->id, $additionalPics);
                        
                        // If NOT in Additional PICs and NOT the main PIC, add to CC
                        if (!$isInAdditionalPics && $pj->id != $laporan->penanggung_jawab_id) {
                            $cc_emails[] = $pj->email;
                        }
                    }
                }
            }

            // Remove duplicates
            $to_emails = array_unique($to_emails);
            $cc_emails = array_unique($cc_emails);

            // Remove TO emails from CC to avoid duplicate
            $cc_emails = array_diff($cc_emails, $to_emails);

            // Send email using Laravel Mailable
            if (count($to_emails) > 0) {
                MailService::to($to_emails)->send(new ReportAssignedMail(
                    $laporan, 
                    array_values($cc_emails),
                    $to_names,
                    $additionalPicNames
                ));
                Log::info("✅ Assigned email sent for report ID: {$laporan->id}");
            } else {
                Log::error("❌ No TO recipients for assigned report ID: {$laporan->id}");
            }

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
            // TO: Main PIC (Person in Charge)
            $to_emails = [];
            $to_names = [];
            $generalPicIds = [];
            
            // First, identify all General PICs in the area
            if ($laporan->area) {
                foreach ($laporan->area->penanggungJawabs as $pj) {
                    if (strcasecmp($pj->station, 'General') === 0) {
                        $generalPicIds[] = $pj->id;
                    }
                }
            }
            
            if ($laporan->penanggungJawab && $laporan->penanggungJawab->email) {
                $to_emails[] = $laporan->penanggungJawab->email;
                $to_names[] = $laporan->penanggungJawab->name;
            } elseif ($laporan->area) {
                foreach ($laporan->area->penanggungJawabs as $pj) {
                    if ($pj->email) {
                        // Only add to TO if NOT a General PIC (General goes to CC by default)
                        if (!in_array($pj->id, $generalPicIds)) {
                            $to_emails[] = $pj->email;
                            $to_names[] = $pj->name;
                        }
                    }
                }
            }

            // Add additional PICs to TO emails and names
            $additionalPicNames = [];
            $additionalPicIds = $laporan->additional_pic_ids ?? [];
            $additionalPicIds = is_array($additionalPicIds) ? array_filter($additionalPicIds) : [];
            
            if (!empty($additionalPicIds)) {
                $additionalPenanggungJawabs = PenanggungJawab::whereIn('id', $additionalPicIds)
                    ->select('id', 'name', 'station', 'email')
                    ->get();
                
                foreach ($additionalPenanggungJawabs as $pj) {
                    $to_names[] = $pj->name;
                    $additionalPicNames[] = $pj->name;
                    
                    if ($pj->email && filter_var($pj->email, FILTER_VALIDATE_EMAIL)) {
                        $to_emails[] = $pj->email;
                    }
                }
            }

            if (empty($to_emails)) {
                Log::warning("No email recipients for edited report ID: {$laporan->id}");
                return;
            }

            // CC: Head/Manager (PICs with station = "General" in the same Area)
            // EXCEPT: If General is in Additional PICs, move to TO instead of CC
            $cc_emails = [];

            if ($laporan->area) {
                // Get all PICs with station "General" in the same area (case-insensitive)
                foreach ($laporan->area->penanggungJawabs as $pj) {
                    if (strcasecmp($pj->station, 'General') === 0 && $pj->email) {
                        // Check if this General PIC is in Additional PICs
                        $isInAdditionalPics = !empty($additionalPicIds) && 
                                            in_array($pj->id, $additionalPicIds);
                        
                        // If NOT in Additional PICs and NOT the main PIC, add to CC
                        if (!$isInAdditionalPics && $pj->id != $laporan->penanggung_jawab_id) {
                            $cc_emails[] = $pj->email;
                        }
                    }
                }
            }

            // Remove duplicates
            $to_emails = array_unique($to_emails);
            $cc_emails = array_unique($cc_emails);

            // Remove TO emails from CC to avoid duplicate
            $cc_emails = array_diff($cc_emails, $to_emails);

            // Send email using Laravel Mailable
            if (count($to_emails) > 0) {
                MailService::to($to_emails)->send(new ReportEditedMail(
                    $laporan, 
                    $perubahan, 
                    array_values($cc_emails),
                    $to_names,
                    $additionalPicNames
                ));
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
            // TO: Main PIC (Person in Charge)
            $to_emails = [];
            $to_names = [];
            $generalPicIds = [];
            
            // First, identify all General PICs in the area
            if ($laporan->area) {
                foreach ($laporan->area->penanggungJawabs as $pj) {
                    if (strcasecmp($pj->station, 'General') === 0) {
                        $generalPicIds[] = $pj->id;
                    }
                }
            }
            
            if ($laporan->penanggungJawab && $laporan->penanggungJawab->email) {
                $to_emails[] = $laporan->penanggungJawab->email;
                $to_names[] = $laporan->penanggungJawab->name;
            } elseif ($laporan->area) {
                // If no specific PIC, send to NON-General PICs in the area
                foreach ($laporan->area->penanggungJawabs as $pj) {
                    if ($pj->email) {
                        // Only add to TO if NOT a General PIC (General goes to CC by default)
                        if (!in_array($pj->id, $generalPicIds)) {
                            $to_emails[] = $pj->email;
                            $to_names[] = $pj->name;
                        }
                    }
                }
            }

            // Add additional PICs to TO emails and names
            $additionalPicNames = [];
            $additionalPicIds = $laporan->additional_pic_ids ?? [];
            $additionalPicIds = is_array($additionalPicIds) ? array_filter($additionalPicIds) : [];
            
            if (!empty($additionalPicIds)) {
                $additionalPenanggungJawabs = PenanggungJawab::whereIn('id', $additionalPicIds)
                    ->select('id', 'name', 'station', 'email')
                    ->get();
                
                foreach ($additionalPenanggungJawabs as $pj) {
                    $to_names[] = $pj->name;
                    $additionalPicNames[] = $pj->name;
                    
                    if ($pj->email && filter_var($pj->email, FILTER_VALIDATE_EMAIL)) {
                        $to_emails[] = $pj->email;
                    }
                }
            }

            if (empty($to_emails)) {
                Log::warning("No email recipients for completed report ID: {$laporan->id}");
                return;
            }

            // CC: Head/Manager (PICs with station = "General" in the same Area)
            // EXCEPT: If General is in Additional PICs, move to TO instead of CC
            $cc_emails = [];

            if ($laporan->area) {
                // Get all PICs with station "General" in the same area (case-insensitive)
                foreach ($laporan->area->penanggungJawabs as $pj) {
                    if (strcasecmp($pj->station, 'General') === 0 && $pj->email) {
                        // Check if this General PIC is in Additional PICs
                        $isInAdditionalPics = !empty($additionalPicIds) && 
                                            in_array($pj->id, $additionalPicIds);
                        
                        // If NOT in Additional PICs and NOT the main PIC, add to CC
                        if (!$isInAdditionalPics && $pj->id != $laporan->penanggung_jawab_id) {
                            $cc_emails[] = $pj->email;
                        }
                    }
                }
            }

            // Remove duplicates
            $to_emails = array_unique($to_emails);
            $cc_emails = array_unique($cc_emails);

            // Remove TO emails from CC to avoid duplicate
            $cc_emails = array_diff($cc_emails, $to_emails);

            // Send email using Laravel Mailable
            if (count($to_emails) > 0) {
                MailService::to($to_emails)->send(new ReportCompletedMail(
                    $laporan, 
                    array_values($cc_emails),
                    $to_names,
                    $additionalPicNames
                ));
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

            // TO: Main PIC (Person in Charge)
            $to_emails = [];
            $to_names = [];
            $generalPicIds = [];
            
            // First, identify all General PICs in the area
            if ($firstReport->area) {
                foreach ($firstReport->area->penanggungJawabs as $pj) {
                    if (strcasecmp($pj->station, 'General') === 0) {
                        $generalPicIds[] = $pj->id;
                    }
                }
            }
            
            if ($firstReport->penanggungJawab && $firstReport->penanggungJawab->email) {
                $to_emails[] = $firstReport->penanggungJawab->email;
                $to_names[] = $firstReport->penanggungJawab->name;
                $pic = $firstReport->penanggungJawab;
            } elseif ($firstReport->area) {
                foreach ($firstReport->area->penanggungJawabs as $pj) {
                    if ($pj->email) {
                        if (!in_array($pj->id, $generalPicIds)) {
                            $to_emails[] = $pj->email;
                            $to_names[] = $pj->name;
                        }
                    }
                }
                $pic = null;
            }

            // Add additional PICs from all reports in this group to TO
            $allAdditionalPicIds = [];
            foreach ($groupReports as $report) {
                $additionalPicIds = $report->additional_pic_ids ?? [];
                $additionalPicIds = is_array($additionalPicIds) ? array_filter($additionalPicIds) : [];
                $allAdditionalPicIds = array_merge($allAdditionalPicIds, $additionalPicIds);
            }
            $allAdditionalPicIds = array_unique($allAdditionalPicIds);

            if (!empty($allAdditionalPicIds)) {
                $additionalPenanggungJawabs = PenanggungJawab::whereIn('id', $allAdditionalPicIds)
                    ->select('id', 'name', 'email')
                    ->get();
                
                foreach ($additionalPenanggungJawabs as $pj) {
                    $to_names[] = $pj->name;
                    
                    if ($pj->email && filter_var($pj->email, FILTER_VALIDATE_EMAIL)) {
                        $to_emails[] = $pj->email;
                    }
                }
            }

            if (empty($to_emails)) {
                Log::warning("No email recipients for reminder group: {$groupKey}");
                continue;
            }

            // CC: Head/Manager (PICs with station = "General" in the same Area)
            // EXCEPT: If General is in Additional PICs, move to TO instead of CC
            $cc_emails = [];
            if ($firstReport->area) {
                foreach ($firstReport->area->penanggungJawabs as $pj) {
                    if (strcasecmp($pj->station, 'General') === 0 && $pj->email) {
                        $isInAdditionalPics = !empty($allAdditionalPicIds) && in_array($pj->id, $allAdditionalPicIds);
                        
                        if (!$isInAdditionalPics && (!$pic || $pj->id != $pic->id)) {
                            $cc_emails[] = $pj->email;
                        }
                    }
                }
            }

            $to_emails = array_unique($to_emails);
            $cc_emails = array_unique($cc_emails);
            $cc_emails = array_diff($cc_emails, $to_emails);

            try {
                $mailable = new $mailableClass($groupReports, $pic, array_values($cc_emails), $to_names);
                MailService::to($to_emails)->send($mailable);

                Log::info("Reminder email sent to: " . implode(', ', $to_emails) . " ({$groupReports->count()} reports)");
            } catch (\Exception $e) {
                Log::error("Failed to send reminder email to: " . implode(', ', $to_emails) . ". Error: " . $e->getMessage());
            }
        }
    }
}
