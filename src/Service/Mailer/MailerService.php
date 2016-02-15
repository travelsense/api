<?php

namespace Service\Mailer;

use Psr\Log\LoggerAwareTrait;
use Silex\Translator;
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
     * @var Translator
     */
    private $translator;

    /**
     * @var array
     */
    private $conf;

    /**
     * MailerService constructor.
     * @param Swift_Mailer $mailer
     * @param Twig_Environment $twig
     * @param Translator $translator
     * @param array $conf
     */
    public function __construct(Swift_Mailer $mailer, Twig_Environment $twig, Translator $translator, array $conf)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->translator = $translator;
        $this->conf = $conf;
    }

    /**
     *
     * @param string $email
     * @param string $token
     */
    public function sendAccountConfirmationMessage($email, $token)
    {
        $subj = $this->translator->trans('acct_confirmation', [], 'email');
        $body = $this->twig->render('email/acct_confirmation.twig', ['token' => $token]);
        $message = Swift_Message::newInstance($subj, $body)
            ->setFrom($this->conf['from_address'], $this->conf['from_name'])
            ->setTo($email);

        $sent = $this->mailer->send($message);
        if ($this->logger) {
            $this->logger->info('Sending account confirmation email',
                [
                    'email' => $email,
                    'token' => $token,
                    'sent' => $sent,
                ]
            );
        }
    }
}
