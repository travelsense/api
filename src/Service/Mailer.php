<?php
namespace Api\Service;

use DateTime;
use DateTimeZone;
use Psr\Log\LoggerAwareTrait;
use Swift_Attachment;
use Swift_Mailer;
use Swift_Message;
use Throwable;
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
        $template = $this->twig->load('email/confirmation.twig');
        $link = sprintf($this->conf['email_confirm'], urlencode($token));
        $message = Swift_Message::newInstance($template->renderBlock('subj', []))
            ->setFrom($this->conf['from_address'], $this->conf['from_name'])
            ->setTo($email)
            ->setBody($template->renderBlock('body', ['link' => $link]));

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
        $template = $this->twig->load('email/reset.twig');

        $link = sprintf($this->conf['password_reset'], urlencode($token));

        $message = Swift_Message::newInstance($template->renderBlock('subj', []))
            ->setFrom($this->conf['from_address'], $this->conf['from_name'])
            ->setTo($email)
            ->setBody($template->renderBlock('body', ['link' => $link]));

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
        $params = [
            'booking' => $booking,
            'date'    => new DateTime('now', new DateTimeZone('UTC')),
        ];
        $pdf = $this->pdf_generator
            ->generate(
                $this->twig
                    ->load('email/booking_pdf.twig')
                    ->render($params)
            );
        $attachment = Swift_Attachment::newInstance($pdf, 'hoptrip_booking.pdf', 'application/pdf');
        $message = Swift_Message::newInstance('HopTrip Booking Request')
            ->setFrom($this->conf['from_address'], $this->conf['from_name'])
            ->setTo($this->conf['booking_details_receivers'])
            ->addPart(
                $this->twig
                    ->load('email/booking.twig')
                    ->render($params),
                'text/html'
            )
            ->attach($attachment)
            ->setBody('');
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

    /**
     * Send statistic data about users and travels
     * @param array              $stats
     * @param \DateTimeInterface $date
     */
    public function sendStats(array $stats, \DateTimeInterface $date)
    {
        $template = $this->twig->load('email/stats.twig');

        $message = Swift_Message::newInstance(
            $template->renderBlock(
                'subj',
                [
                    'stats' => $stats,
                    'date'  => $date,
                ]
            )
        )
            ->setFrom($this->conf['from_address'], $this->conf['from_name'])
            ->setTo($this->conf['stats_receivers'])
            ->setBody(
                $template->renderBlock(
                    'body',
                    [
                        'stats' => $stats,
                        'date'  => $date,
                    ]
                )
            );
        $sent = $this->mailer->send($message);
        if ($this->logger) {
            $this->logger->info(
                'Sending statistic details',
                [
                    'sent'    => $sent,
                    'Users'   => $stats['users'],
                    'Travels' => $stats['travels'],
                ]
            );
        }
    }

    /**
     * Send error message
     * @param Throwable $e
     */
    public function sendErrorMessage(Throwable $e)
    {
        $template = $this->twig->load('email/error.twig');

        $message = Swift_Message::newInstance(
            $template->renderBlock(
                'subj',
                [
                    'e' => $e,
                ]
            )
        )
            ->setFrom($this->conf['from_address'], $this->conf['from_name'])
            ->setTo($this->conf['error_message'])
            ->setBody(
                $template->renderBlock(
                    'body',
                    [
                        'e' => $e,
                    ]
                )
            );
        $sent = $this->mailer->send($message);
        if ($this->logger) {
            $this->logger->info(
                'Sending error message',
                [
                    'sent'    => $sent,
                    'Status'  => $e->getCode(),
                    'Message' => $e->getMessage(),
                    'File'    => $e->getFile() . "(" . $e->getLine() . ")",
                ]
            );
        }
    }
}
