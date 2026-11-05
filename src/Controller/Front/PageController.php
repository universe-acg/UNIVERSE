<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Controller\Front;

use App\Service\AppServices;
use App\Service\NotificationService;
use App\Service\DemoMailer as Swift_Mailer;
use App\Service\MailMessage as Swift_Message;
use App\Controller\BaseController as AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PageController extends AbstractController
{

    private $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     *
     */
#[Route("/page/{slug}", name: "page")]
    public function page($slug, AppServices $services): Response
    {
        $page = $services->getPages(array('slug' => $slug))->getQuery()->getOneOrNullResult();

        if (!$page) {
            $this->addFlash("error", "The page can not be found");
            return $this->redirectToRoute("homepage");
        }

        return $this->render('Front/Page/page.html.twig', [
            "page" => $page
        ]);
    }

    /**
     *
     */
#[Route("/contact", name: "contact")]
    public function contact(Request $request, Swift_Mailer $mailer, AppServices $services): Response
    {

        $form = $this->createFormBuilder()
            ->add('name', TextType::class, [
                'required' => true,
                'label' => 'Name',
                'constraints' => array(// new NotBlank()
                ),
            ])
            ->add('email', TextType::class, [
                'required' => true,
                'label' => 'Email',
                'constraints' => array(




                ),
            ])
            ->add('subject', TextType::class, [
                'required' => true,
                'label' => 'Subject',
                'constraints' => array(






                ),
            ])
            ->add('message', TextareaType::class, [
                'required' => true,
                'label' => 'Message',
                'attr' => ['rows' => '10'],
                'constraints' => array(






                ),
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Send',
                'attr' => ['class' => 'btn btn-primary btn-block'],
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $data = $form->getData();

                $email = (new Swift_Message($services->getSetting("website_name") . " contact form"))
                    ->setFrom($services->getSetting('no_reply_email'))
                    ->setTo(['akourtisdev@gmail.com', 'alexandros.kourtis@acg.edu', $services->getSetting("contact_email"), 'universeaug@gmail.com'])
                    ->setBody("Sender: " . $data["name"] . "\r\n" .
                        "Email: " . $data["email"] . "\r\n" .
                        "Subject: " . $data["subject"] . "\r\n" .
                        "Message: " . $data["message"] . "\r\n");

                $mailer->send($email);

                $this->notificationService->createNotification($this->getUser()->getId(), $message, $email->getSubject());

                $this->addFlash('success', 'Your message has been successfully sent');
            } else {
                $this->addFlash('error', 'The form contains invalid data');
            }
        }

        return $this->render('Front/Page/contact.html.twig', ['form' => $form->createView()]);
    }

    /**
     *
     */
#[Route("/access-denied", name: "access_denied")]
    public function accessDenied(): Response
    {
        return $this->render('Front/Page/access-denied.html.twig');
    }
}
