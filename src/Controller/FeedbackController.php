<?php

namespace App\Controller;

use App\Form\FeedbackContactType;
use App\Service\LangHelper;
use App\Service\NavHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment as TwigEnvironment;

class FeedbackController extends AbstractController
{
    public function __construct(
        private readonly LangHelper $langHelper,
        private readonly NavHelper $navHelper,
        private readonly TwigEnvironment $twig,
        private readonly EntityManagerInterface $em,
    )
    {
    }

    #[Route('/{lang}/feedback', name: 'site_feedback')]
    public function index(Request $request, string $lang): Response
    {
        return $this->render($lang.'/feedback/index.html.twig', [
            'controller_name' => 'FeedbackController',
        ]);
    }

    #[Route('/{lang}/contacts', name: 'feedback_contacts')]
    public function contacts(Request $request, string $lang): Response
    {
        $view = $lang . '/contacts.html.twig';
        $form = $this->createForm(FeedbackContactType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Process the form data
            $entity = $form->getData();
            $this->em->persist($entity);
            $this->em->flush();

            // Redirect or render a response
            return $this->redirectToRoute('feedback_sent', ['lang' => $lang]);
        }

        $form->get('type')->setData('contact');

        return $this->render($view, [
            'lang' => $lang,
            'navHelper' => $this->navHelper,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{lang}/order', name: 'feedback_order')]
    public function order(Request $request, string $lang): Response
    {
        $view = $lang . '/contacts.html.twig';
        $form = $this->createForm(FeedbackContactType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Process the form data
            $entity = $form->getData();
            $this->em->persist($entity);
            $this->em->flush();

            // Redirect or render a response
            return $this->redirectToRoute('feedback_sent', ['lang' => $lang]);
        }

        $form->get('type')->setData('order');

        if ($subject = $request->get('subject')) {
            $form->get('text')->setData(
                sprintf('Замаўляю: %s', $subject)
            );
        }


        return $this->render($view, [
            'lang' => $lang,
            'navHelper' => $this->navHelper,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{lang}/sent', name: 'feedback_sent')]
    public function success(Request $request, string $lang): Response
    {
        $view = $lang . '/feedback_sent.html.twig';

        return $this->render($view, [
            'lang' => $lang,
            'navHelper' => $this->navHelper,
        ]);
    }
}
