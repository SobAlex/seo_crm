<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $url;
    public $teamName;
    public $role;

    public function __construct(string $url, string $teamName, string $role)
    {
        $this->url = $url;
        $this->teamName = $teamName;
        $this->role = $role;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Приглашение в компанию {$this->teamName}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.invitation',
            with: [
                'url' => $this->url,
                'teamName' => $this->teamName,
                'role' => $this->role === 'employee' ? 'Сотрудника' : 'Подрядчика',
            ]
        );
    }
}
