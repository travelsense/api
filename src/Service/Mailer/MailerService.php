<?php

namespace Service\Mailer;

use Swift_Mailer;
use Swift_Message;
use Twig_Environment;

class MailerService
{
    /**
     * @var Swift_Mailer
     */
    private $mailer;

    /**
     * @var Twig_Environment
     */
    private $twig;

    /**
     * @var array
     */
    private $conf;

    /**
     * MailerService constructor.
     * @param Swift_Mailer $mailer
     * @param Twig_Environment $twig
     * @param array $conf
     */
    public function __construct(Swift_Mailer $mailer, Twig_Environment $twig, array $conf)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->conf = $conf;
    }

    /**
     *
     * @param string $email
     * @param string $token
     */
    public function sendAccountConfirmationMessage($email, $token)
    {
        $message = Swift_Message::newInstance()
            ->setSubject($this->twig->render('email/acct_confirmation_subj.twig'))
            ->setFrom($this->conf['from_address'], $this->conf['from_name'])
            ->setTo($email)
            ->setBody($this->twig->render('email/acct_confirmation_body.twig', ['token' => $token]));

        $this->mailer->send($message);

    }
}
