<?php

namespace Api\Service\Mailer;

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
    public function sendAccountConfirmationMessage($email, $token)
    {
        $emailTpl = $this->twig->loadTemplate('email/confirmation.twig');

        $link = "https://travelsen.se/#confirm/$token";

        $message = Swift_Message::newInstance($emailTpl->renderBlock('subj', []))
            ->setBody($emailTpl->renderBlock('body', ['link' => $link]))
            ->setFrom($this->conf['from_address'], $this->conf['from_name'])
            ->setTo($email);

        $sent = $this->mailer->send($message);
        if ($this->logger) {
            $this->logger->info(
                'Sending account confirmation email',
                [
                    'email' => $email,
                    'token' => $token,
                    'sent' => $sent,
                ]
            );
        }
    }

    /**
     * @param $email
     * @param $token
     */
    public function sendPasswordResetLink($email, $token)
    {
        $emailTpl = $this->twig->loadTemplate('email/reset.twig');

        $link = "https://travelsen.se/#reset/$token";

        $message = Swift_Message::newInstance($emailTpl->renderBlock('subj', []))
            ->setBody($emailTpl->renderBlock('body', ['link' => $link]))
            ->setFrom($this->conf['from_address'], $this->conf['from_name'])
            ->setTo($email);

        $sent = $this->mailer->send($message);
        if ($this->logger) {
            $this->logger->info(
                'Sending password reset link',
                [
                    'email' => $email,
                    'token' => $token,
                    'sent' => $sent,
                ]
            );
        }
    }
}
