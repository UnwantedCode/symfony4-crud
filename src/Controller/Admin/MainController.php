<?php

namespace App\Controller\Admin;

use App\Entity\Video;
use App\Form\UserType;
use App\Utils\CategoryTreeAdminOptionList;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
/**
 * @Route("/admin")
 */
class MainController extends AbstractController
{
    /**
     * @Route("/", name="admin_main_page")
     */
    public function index(Request $request, UserPasswordEncoderInterface $passwordEncoder, TranslatorInterface $translator)
    {
        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user, ['user' => $user]);
        $form->handleRequest($request);
        $isInvalid = false;
        if($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $user->setName($request->request->get('user')['name']);
            $user->setLastName($request->request->get('user')['last_name']);
            $user->setEmail($request->request->get('user')['email']);
            $password = $passwordEncoder->encodePassword($user, $request->request->get('user')['password']['first']);
            $user->setPassword($password);
            $em->persist($user);
            $em->flush();


            $this->addFlash('success', 'Profile updated');
        }
        elseif($form->isSubmitted() && !$form->isValid())
        {
            $isInvalid = 'is-invalid';
        }

        return $this->render('admin/my_profile.html.twig', [
            'subscription' => $this->getUser()->getSubscription(),
            'form' => $form->createView(),
            'isInvalid' => $isInvalid,
        ]);
    }

    /**
     * @Route({"en":"/videos","pl":"/lista-wideo"}, name="videos")
     */
    public function videos(CategoryTreeAdminOptionList $categories)
    {
        if($this->isGranted('ROLE_ADMIN'))
        {
            $categories->getCategoryList($categories->buildTree());
            $videos = $this->getDoctrine()->getRepository(Video::class)->findBy([],['title' => 'ASC']);
        } else {
            $categories = null;
            $videos = $this->getUser()->getLikedVideos();
        }
        return $this->render('admin/videos.html.twig', [
            'videos' => $videos,
            'categories' => $categories,
        ]);
    }



    /**
     * @Route("/cancel-plan", name="cancel_plan")
     */
    public function cancelPlan()
    {
        $user = $this->getUser();
        $subscription = $user->getSubscription();
        $subscription->setValidTo(new \DateTime());
        $subscription->setPaymentStatus(null);
        $subscription->setPlan('canceled');

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->persist($subscription);
        $em->flush();
        return $this->redirectToRoute('admin_main_page');
    }

    // delete account
    /**
     * @Route("/delete-account", name="delete_account")
     */
    public function deleteAccount()
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();
        session_destroy();
        return $this->redirectToRoute('main_page');
    }



}