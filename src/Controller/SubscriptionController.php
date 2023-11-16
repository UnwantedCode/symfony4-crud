<?php

namespace App\Controller;

use App\Entity\Subscription;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SubscriptionController extends AbstractController
{
    /**
     * @Route("/pricing", name="pricing")
     */
    public function pricing()
    {
        return $this->render('front/pricing.html.twig', [
            'name' => Subscription::getPlanDataNames(),
            'price' => Subscription::getPlanDataPrices(),
        ]);
    }
}
