<?php

namespace Service\Mailer;

use Mandrill_Messages;
use Twig_Environment;

class MailerService
{
    /**
     * @var Mandrill_Messages
     */
    private $messages;

    /**
     * @var Twig_Environment
     */
    private $twig;

    /**
     * MailerService constructor.
     * @param Mandrill_Messages $messages
     * @param Twig_Environment $twig
     */
    public function __construct(Mandrill_Messages $messages, Twig_Environment $twig)
    {
        $this->messages = $messages;
        $this->twig = $twig;
    }

    /**
     *
     * @param string $email
     * @param string $token
     */
    public function sendAccountConfirmationMessage($email, $token)
    {
        $body = $this->twig->render('email/user-acct-confirmation.twig', ['token' => $token]);
        $message = [
            'text' => $body,
            'subject' => 'Acct confirmation',
            'to' => [
                ['email' => $email],
            ],
            'from_email' => 'info@vacarious.org',
            'from_name' => 'Vacarious',
            'headers' => [
                'Reply-To' => 'info@vacarious.org',
            ],
            'tags' => ['password-resets'],
        ];
        $this->messages->send($message);
    }
}