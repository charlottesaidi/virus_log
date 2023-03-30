<?php

namespace App\Service\Mailer;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Exception\InvalidArgumentException;
use Symfony\Component\Mime\Exception\LogicException;

class MailerService {
    /**
     * @var MailerInterface
     */
    private MailerInterface $mailer;

    /**
     * @param MailerInterface $mailer 
     * @return void 
     */
    public function __construct(MailerInterface $mailer) {
        $this->mailer = $mailer;
    }

    /**
     * Send an email
     * 
     * @param string $from 
     * @param string $to 
     * @param string $subject 
     * @param string $htmlTemplate // Path for email template // ex : 'email/contact.html.twig'
     * @param array $context // Keys => Values for dynamic content in mail // ex : [ 'first_name' => 'Michel' ]
     * @param int $priority 
     * @return void 
     * @throws InvalidArgumentException 
     * @throws LogicException 
     * @throws TransportExceptionInterface 
     */
    public function send(string $from, string $to, string $subject, string $htmlTemplate, array $context = [], int $priority = 1) {
        $email = (new TemplatedEmail())
            ->from($from)
            ->to($to)
            ->subject($subject)
            ->htmlTemplate($htmlTemplate)
            ->priority($priority)
            ->context($context);

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            throw new TransportException('Une erreur est survenue lors de l\'envoi. Veuillez réessayer plus tard.');
        }
    }
}
