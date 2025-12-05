<?php

namespace App\Mail;

use App\Models\Member;
use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Bus\Queueable;

class MemberRegistrationMail extends Mailable
{
    use Queueable, SerializesModels;

   public $member;
    public $password;
    public $isNewUser;
    public $user; // ADD THIS

    public function __construct(Member $member, ?string $password, bool $isNewUser, User $user)
    {
        $this->member = $member;
        $this->password = $password;
        $this->isNewUser = $isNewUser;
        $this->user = $user; // SAVE USER
    }

    public function build()
    {
        return $this->subject(
            $this->isNewUser
                ? ('Welcome to ' . $this->member->business->name)
                : ('Access Granted to ' . $this->member->business->name)
        )
        ->view('emails.member-registration')
        ->with([
            'member' => $this->member,
            'password' => $this->password,
            'isNewUser' => $this->isNewUser,
            'loginUrl' => url('/login'),
            'user' => $this->user, // SEND USER TO VIEW
        ]);
    }
}
