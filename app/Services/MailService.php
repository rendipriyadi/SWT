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
     * Send email using Laravel Mailable
     *
     * Usage: MailService::to($emails)->send(new ReportAssignedMail(...));
     *
     * @param Mailable $mailable - Laravel Mailable instance
     * @return void
     */
    public function send(Mailable $mailable)
    {

        $mailable->withSymfonyMessage(function ($message) {
            // Get Symfony Email object
            $symfonyEmail = $message;

            // Path to your PKCS#12 certificate (Ensure it contains BOTH private key & certificate)
            // $pkcs12Path = "C:/SoftKeyOneX2025.pem";
            $pkcs12Path = "D:/Apache24/SoftKeyOneX2025.pem";
            $pkcs12Password = getenv("ONEX_CERTIFICATE_CREDENTIAL");

            try {
                // Create SMimeSigner instance
                $signer = new SMimeSigner($pkcs12Path, $pkcs12Path, $pkcs12Password);

                // Sign the Symfony email
                $signedEmail = $signer->sign($symfonyEmail);

                // Replace the original message with the signed message
                $message->setBody($signedEmail->getBody());
                $symfonyEmail->setHeaders($signedEmail->getHeaders());
            } catch (\Exception $e) {
                Log::error("Email signing failed: " . $e->getMessage());
            }
        });


        Mail::to($this->to_email)->send($mailable);
    }
}
