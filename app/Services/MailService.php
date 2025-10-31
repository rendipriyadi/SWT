<?php

namespace App\Services;

use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mime\Crypto\SMimeSigner;

class MailService
{
    protected $to_email;

    /**
     * Set email recipient(s)
     * 
     * @param string|array $to_email
     * @return self
     */
    public static function to($to_email)
    {
        $instance = new self();
        $instance->to_email = $to_email;
        return $instance;
    }

    /**
     * Send email using Laravel Mailable with optional digital signature
     * 
     * Usage: MailService::to($emails)->send(new ReportAssignedMail(...));
     * 
     * @param Mailable $mailable - Laravel Mailable instance
     * @return void
     */
    public function send(Mailable $mailable)
    {
        // ============================================================================
        // DIGITAL SIGNATURE (S/MIME) - Currently disabled
        // Uncomment code below to enable email signing with digital certificate
        // ============================================================================
        /*
        // Add callback to sign email before sending
        $mailable->withSymfonyMessage(function ($message) {
            $pkcs12Path = "D:/Apache24/YourCertificate.pem"; // Update path
            $pkcs12Password = env('CERTIFICATE_PASSWORD');

            try {
                $signer = new SMimeSigner($pkcs12Path, $pkcs12Path, $pkcs12Password);
                $signedEmail = $signer->sign($message);
                
                // Replace message with signed version
                $message->setBody($signedEmail->getBody());
                $message->setHeaders($signedEmail->getHeaders());
                
                Log::info("Email signed successfully with digital signature");
            } catch (\Exception $e) {
                Log::error("Email signing failed: " . $e->getMessage());
            }
        });
        */

        // Send email using standard Laravel Mail
        Mail::to($this->to_email)->send($mailable);
    }
}
