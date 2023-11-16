<?php

namespace App\Controller;

use App\Controller\Traits\SaveSubscription;
use App\Entity\Subscription;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SubscriptionController extends AbstractController
{
    use SaveSubscription;
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

    /**
     * @Route("/payment/{paypal}", name="payment", defaults={"paypal" = null})
     */
    public function payment($paypal, SessionInterface $session)

    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
        if ($paypal)
        {
            $this->saveSubscription($session->get('planName'), $this->getUser());
            return $this->redirectToRoute('admin_main_page');
        }
        return $this->render('front/payment.html.twig');
    }
}
