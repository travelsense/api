<?php

namespace Api\Service;

use DateTime;
use DateTimeZone;
use Psr\Log\LoggerAwareTrait;
use Swift_Attachment;
use Swift_Mailer;
use Swift_Message;
use Twig_Environment;

class Mailer
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
     * @var PdfGenerator
     */
    private $pdf_generator;

    public function __construct(Swift_Mailer $mailer, Twig_Environment $twig, PdfGenerator $pdf_generator, array $conf)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->pdf_generator = $pdf_generator;
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
     * @param array $booking
     */
    public function sendBookingDetails(array $booking)
    {
        $template = $this->twig->loadTemplate('email/booking.twig');
        $html = $template->render([
            'booking' => $booking,
            'date' => new DateTime('now', new DateTimeZone('UTC')),
        ]);
        $pdf = $this->pdf_generator->generate($html);
        $attachment = Swift_Attachment::newInstance($pdf, 'hoptrip_booking.pdf', 'application/pdf');
        $message = Swift_Message::newInstance('HopTrip Booking Request')
            ->setFrom($this->conf['from_address'], $this->conf['from_name'])
            ->setTo($this->conf['booking_details_receivers'])
            ->attach($attachment)
        ;
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
