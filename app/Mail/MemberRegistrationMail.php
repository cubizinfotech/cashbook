<?php

namespace App\Mail;

use App\Models\Member;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MemberRegistrationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $member;
    public $password;

    /**
     * Create a new message instance.
     */
    public function __construct(Member $member, string $password)
    {
        $this->member = $member;
        $this->password = $password;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Welcome to ' . $this->member->business->name)
            ->view('emails.member-registration')
            ->with([
                'member' => $this->member,
                'password' => $this->password,
                'loginUrl' => url('/login'),
            ]);
    }
}

