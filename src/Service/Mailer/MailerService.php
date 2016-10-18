<?php

namespace Api\Service\Mailer;

use Psr\Log\LoggerAwareTrait;
use Swift_Mailer;
use Swift_Message;
use Twig_Environment;

class MailerService
{
    use LoggerAwareTrait;

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
     *
     * @param Swift_Mailer     $mailer
     * @param Twig_Environment $twig
     * @param array            $conf
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
    public function sendAccountConfirmationMessage(string $email, string $token)
    {
        $template = $this->twig->loadTemplate('email/confirmation.twig');

        $link = sprintf($this->conf['email_confirm'], urlencode($token));

        $message = Swift_Message::newInstance($template->renderBlock('subj', []))
            ->setBody($template->renderBlock('body', ['link' => $link]))
            ->setFrom($this->conf['from_address'], $this->conf['from_name'])
            ->setTo($email);

        $sent = $this->mailer->send($message);
        if ($this->logger) {
            $this->logger->info(
                'Sending account confirmation email',
                [
                    'email' => $email,
                    'token' => $token,
                    'sent'  => $sent,
                ]
            );
        }
    }

    /**
     * @param $email
     * @param $token
     */
    public function sendPasswordResetLink(string $email, string $token)
    {
        $template = $this->twig->loadTemplate('email/reset.twig');

        $link = sprintf($this->conf['password_reset'], urlencode($token));

        $message = Swift_Message::newInstance($template->renderBlock('subj', []))
            ->setBody($template->renderBlock('body', ['link' => $link]))
            ->setFrom($this->conf['from_address'], $this->conf['from_name'])
            ->setTo($email);

        $sent = $this->mailer->send($message);
        if ($this->logger) {
            $this->logger->info(
                'Sending password reset link',
                [
                    'email' => $email,
                    'token' => $token,
                    'sent'  => $sent,
                ]
            );
        }
    }

    /**
     * Send booking details to internal address
     * @param string $details
     */
    public function sendBookingDetails(string $details)
    {
        $message = Swift_Message::newInstance('Hoptrip Booking Request')
            ->setBody($details)
            ->setFrom($this->conf['from_address'], $this->conf['from_name'])
            ->setTo($this->conf['booking_details_receivers']);

        $sent = $this->mailer->send($message);
        if ($this->logger) {
            $this->logger->info(
                'Sending booking details',
                [
                    'sent' => $sent,
                ]
            );
        }
    }
}
