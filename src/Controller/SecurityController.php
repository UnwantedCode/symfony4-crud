<?php

namespace App\Controller;

use App\Controller\Traits\SaveSubscription;
use App\Entity\Subscription;
use App\Entity\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    use SaveSubscription;
    /**
     * @Route("/register/{plan}", name="register", defaults={"plan"=null})
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, SessionInterface $session, $plan)
    {
        if ($request->isMethod('GET'))
        {
            dump($plan);
            $session->set('planName', $plan);
            $session->set('planPrice', Subscription::getPlanDataPriceByName($plan));
        }

        $user = new User;
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $enrityManager = $this->getDoctrine()->getManager();
            $user->setName($request->request->get('user')['name']);
            $user->setLastName($request->request->get('user')['last_name']);
            $user->setEmail($request->request->get('user')['email']);
            $password = $passwordEncoder->encodePassword($user, $request->request->get('user')['password']['first']);
            $user->setPassword($password);
            $user->setRoles(['ROLE_USER']);

            $date = new \DateTime();
            $date->modify('+1 month');
            $subscription = new Subscription();
            $subscription->setPlan($session->get('planName'));
            $subscription->setValidTo($date);
            if ($plan == Subscription::getPlanDataNameByIndex(0))
            {
                $subscription->setFreePlanUsed(true);
                $subscription->setPaymentStatus('paid');
            }
            $user->setSubscription($subscription);

            $enrityManager->persist($user);
            $enrityManager->flush();
            $this->loginUserAutomatically($user, $password);
            return $this->redirectToRoute('admin_main_page');

        }

        if($this->isGranted('IS_AUTHENTICATED_REMEMBERED') && $plan == Subscription::getPlanDataNameByIndex(0)) // is free
        {
            $this->saveSubscription($plan, $this->getUser());
            return $this->redirectToRoute('admin_main_page');
        }
        elseif ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')) // is not free
        {
            return $this->redirectToRoute('payment');
        }
        return $this->render('front/register.html.twig', [
            'form' => $form->createView(),
        ] );
    }

    /**
     * @Route("/login", name="login")
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        return $this->render('front/login.html.twig' , [
            'error' => $authenticationUtils->getLastAuthenticationError(),
        ]);
    }
    private function loginUserAutomatically($user, $password)
    {
        $token = new UsernamePasswordToken($user, $password, 'main', $user->getRoles());
        $this->get('security.token_storage')->setToken($token);
        $this->get('session')->set('security_main', serialize($token));
    }
    /**
     * @Route("/logout", name="logout")
     */
    public function logout(): void
    {
        throw new \Exception('This should never ne reached!');
    }

}