<?php
// src/Service/MailService.php
namespace App\Service;

use App\Entity\Command;
use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Security\Core\Security;

class MailService
{

    public function __construct(Security $security, MailerInterface $mailer)
    {
        $this->security = $security;
        $this->mailer = $mailer;
    }

    public function sendEmail(User $user, Command $command)
    {
        $email = (new TemplatedEmail())
            ->from(Address::create('MaBoutique.com <notification@ma-boutique.com>'))
            ->to($this->security->getUser()->getUserIdentifier())
            ->subject('Your order has been placed')
            ->htmlTemplate('mail/confirm.html.twig')
            ->context([
                'command' => $command,
                'user' => $user
            ]);

        $this->mailer->send($email);

        return new Response('Email sent');
    }
}
