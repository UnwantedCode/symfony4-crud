<?php

namespace App\Listeners;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use App\Entity\Video;
use App\Entity\User;
class NewVideoListener
{
    public function __construct(\Twig\Environment $templating, \Swift_Mailer $mailer)
    {
        $this->templating = $templating;
        $this->mailer = $mailer;
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if (!$entity instanceof Video) {
            return;
        }
        $entityManager = $args->getObjectManager();
        $users = $entityManager->getRepository(User::class)->findAll();
        foreach ($users as $user) {
            $message = (new \Swift_Message('New video'))
                ->setFrom('send@example.com')
                ->setTo($user->getEmail())
                ->setBody(
                    $this->templating->render(
                        'emails/new_video.html.twig',
                        ['video' => $entity, 'name' => $user->getName()]
                    ),
                    'text/html'
                );
            $this->mailer->send($message);
        }
        $entityManager->flush();
    }
}