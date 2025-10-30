<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Transport\Smtp\SmtpTransport;
use Symfony\Component\Mailer\Transport\Smtp\Stream\SocketStream;

class MailServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Override SMTP transport to disable STARTTLS for Mailtrap
        Mail::extend('mailtrap', function () {
            $config = config('mail.mailers.smtp');
            
            // Create socket stream without TLS
            $stream = new SocketStream();
            
            // Create SMTP transport (not ESMTP to avoid STARTTLS)
            $transport = new SmtpTransport($stream);
            $transport->setHost($config['host']);
            $transport->setPort($config['port']);
            $transport->setUsername($config['username']);
            $transport->setPassword($config['password']);
            
            return $transport;
        });
    }
}
