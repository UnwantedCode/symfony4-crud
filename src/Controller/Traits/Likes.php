<?php

namespace App\Controller\Traits;
use App\Entity\User;
trait Likes
{
    private function likeVideo($video)
    {
        $user = $this->getUser();
        $user->addLikedVideo($video);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();
        return 'liked';
    }
    private function dislikeVideo($video)
    {
        $user = $this->getUser();
        $user->addDislikedVideo($video);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();
        return 'disliked';
    }
    private function undoLikeVideo($video)
    {
        $user = $this->getUser();
        $user->removeLikedVideo($video);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();
        return 'undo liked';
    }
    private function undoDislikeVideo($video)
    {
        $user = $this->getUser();
        $user->removeDislikedVideo($video);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();
        return 'undo disliked';
    }
}