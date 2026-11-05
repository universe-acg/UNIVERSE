<?php
namespace App\Controller;
use App\Entity\User;
use App\Security\UserManager;
use App\Service\DemoMailer;
use App\Service\MailMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\{EmailType,TextType,PasswordType,RepeatedType,ChoiceType,FormType};
use Symfony\Component\HttpFoundation\{Request,Response};
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Constraints as Assert;

final class IdentityController extends BaseController
{
    #[Route('/signup/attendee',name:'fos_user_registration_register_attendee',methods:['GET','POST'])]
    #[Route('/signup/organizer',name:'fos_user_registration_register_organizer',methods:['GET','POST'])]
    public function register(Request $request,UserManager $manager,EntityManagerInterface $em): Response {
        $organizer=$request->attributes->get('_route')==='fos_user_registration_register_organizer';
        $builder=$this->createFormBuilder()->add('firstname',TextType::class,['attr'=>['placeholder'=>'Enter your first name'],'constraints'=>[new Assert\NotBlank(),new Assert\Length(max:80)]])->add('lastname',TextType::class,['attr'=>['placeholder'=>'Enter your last name'],'constraints'=>[new Assert\NotBlank(),new Assert\Length(max:80)]])
            ->add('username',TextType::class,['attr'=>['placeholder'=>'Choose a username'],'constraints'=>[new Assert\Regex('/^[a-zA-Z0-9_-]{3,40}$/')]])->add('email',EmailType::class,['attr'=>['placeholder'=>'Enter your email address'],'constraints'=>[new Assert\Email(),new Assert\NotBlank()]])->add('plainPassword',RepeatedType::class,['type'=>PasswordType::class,'constraints'=>[new Assert\Length(min:8,max:200)],'first_options'=>['label'=>'Password'],'second_options'=>['label'=>'Repeat password']]);
        if($organizer){$builder->add($builder->create('organizer',FormType::class)->add('name',TextType::class,['constraints'=>[new Assert\NotBlank(),new Assert\Length(max:255)]])->add('category',ChoiceType::class,['choices'=>['Club'=>'Club','Society'=>'Society','Organization'=>'Organization']]));}
        else{$builder->add('studentid',TextType::class,['attr'=>['placeholder'=>'Enter your ACG Student ID'],'constraints'=>[new Assert\NotBlank(),new Assert\Length(max:80)]]);}
        $form=$builder->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $data=$form->getData();
            $duplicate=$em->getRepository(User::class)->createQueryBuilder('u')->where('u.usernameCanonical=:username OR u.emailCanonical=:email')->setParameter('username',mb_strtolower($data['username']))->setParameter('email',mb_strtolower($data['email']))->getQuery()->getOneOrNullResult();
            if($duplicate)$form->addError(new \Symfony\Component\Form\FormError('That username or email is already in use.'));
            else {
                $user=new User();$user->setUsername($data['username'])->setEmail($data['email']);$user->setFirstname($data['firstname']);$user->setLastname($data['lastname']);$user->setStudentid($data['studentid']??'DEMO-'.bin2hex(random_bytes(5)));$user->setRoles([$organizer?'ROLE_ORGANIZER':'ROLE_ATTENDEE']);$user->setPlainPassword($data['plainPassword']);
                if($organizer){$organization=new \App\Entity\Organizer();$organization->setName($data['organizer']['name']);$organization->setCategory($data['organizer']['category']);$organization->setUser($user);$user->setOrganizer($organization);}
                $manager->updateUser($user);$request->getSession()->set('registered_username',$user->getUsername());
                return $this->redirectToRoute('fos_user_registration_confirmed');
            }
        }
        return $this->render('bundles/FOSUserBundle/Registration/register-'.($organizer?'organizer':'attendee').'.html.twig',['form'=>$form->createView()]);
    }
    #[Route('/signup/confirmed',name:'fos_user_registration_confirmed',methods:['GET'])]
    #[Route('/signup/check-email',name:'fos_user_registration_check_email',methods:['GET'])]
    public function registered(Request $request): Response { return $this->render('bundles/FOSUserBundle/Registration/confirmed.html.twig',['user'=>['username'=>$request->getSession()->get('registered_username','')],'targetUrl'=>$this->generateUrl('fos_user_security_login')]); }
    #[Route('/resetting/request',name:'fos_user_resetting_request',methods:['GET'])]
    #[Route('/resetting/send-email',name:'fos_user_resetting_send_email',methods:['POST'])]
    public function requestReset(Request $request,EntityManagerInterface $em,DemoMailer $mailer): Response {
        if($request->isMethod('POST')) {
            if(!$this->isCsrfTokenValid('password_reset',$request->request->get('_csrf_token')))throw $this->createAccessDeniedException('Invalid reset request.');
            $identifier=mb_strtolower(trim((string)$request->request->get('username','')));
            $user=$em->getRepository(User::class)->createQueryBuilder('u')->where('u.usernameCanonical=:identifier OR u.emailCanonical=:identifier')->setParameter('identifier',$identifier)->setMaxResults(1)->getQuery()->getOneOrNullResult();
            if($user && $user->isEnabled() && (!$user->getPasswordRequestedAt() || $user->getPasswordRequestedAt()<new \DateTime('-5 minutes'))) {
                $token=bin2hex(random_bytes(32));$user->setConfirmationToken(hash('sha256',$token));$user->setPasswordRequestedAt(new \DateTime());$em->flush();
                $url=$this->generateUrl('fos_user_resetting_reset',['token'=>$token],UrlGeneratorInterface::ABSOLUTE_URL);
                $mailer->send((new MailMessage('UNIVERSE demo password reset'))->from('noreply@universe.example.test')->to($user->getEmail())->text('Reset your demo password within one hour: '.$url));
            }
            return $this->redirectToRoute('fos_user_resetting_check_email');
        }
        return $this->render('bundles/FOSUserBundle/Resetting/request.html.twig');
    }
    #[Route('/resetting/check-email',name:'fos_user_resetting_check_email',methods:['GET'])]
    public function resetRequested(): Response { return $this->render('bundles/FOSUserBundle/Resetting/check_email.html.twig',['tokenLifetime'=>1]); }
    #[Route('/resetting/reset/{token}',name:'fos_user_resetting_reset',methods:['GET','POST'])]
    public function reset(Request $request,string $token,EntityManagerInterface $em,UserManager $manager): Response {
        $user=$em->getRepository(User::class)->findOneBy(['confirmationToken'=>hash('sha256',$token)]);
        if(!$user || !$user->getPasswordRequestedAt() || $user->getPasswordRequestedAt()<new \DateTime('-1 hour'))throw $this->createNotFoundException('Reset link is invalid or expired.');
        $form=$this->passwordForm(false);$form->handleRequest($request);
        if($form->isSubmitted()&&$form->isValid()){$user->setPlainPassword($form->get('password')->getData());$user->setConfirmationToken(null);$user->setPasswordRequestedAt(null);$manager->updateUser($user);return $this->redirectToRoute('fos_user_security_login');}
        return $this->render('bundles/FOSUserBundle/Resetting/reset.html.twig',['form'=>$form->createView(),'token'=>$token]);
    }
    #[Route('/dashboard/change-password',name:'fos_user_change_password',methods:['GET','POST'])]
    public function changePassword(Request $request,UserManager $manager,UserPasswordHasherInterface $hasher): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');$form=$this->passwordForm(true);$form->handleRequest($request);
        if($form->isSubmitted()&&$form->isValid()) {
            if(!$hasher->isPasswordValid($this->getUser(),$form->get('currentPassword')->getData()))$form->addError(new \Symfony\Component\Form\FormError('Current password is incorrect.'));
            else{$this->getUser()->setPlainPassword($form->get('password')->getData());$manager->updateUser($this->getUser());return $this->redirectToRoute('dashboard_index');}
        }
        return $this->render('bundles/FOSUserBundle/ChangePassword/change_password.html.twig',['form'=>$form->createView()]);
    }
    private function passwordForm(bool $current): \Symfony\Component\Form\FormInterface {
        $builder=$this->createFormBuilder();if($current)$builder->add('currentPassword',PasswordType::class,['constraints'=>[new Assert\NotBlank()]]);
        return $builder->add('password',RepeatedType::class,['type'=>PasswordType::class,'constraints'=>[new Assert\Length(min:8,max:200)],'first_options'=>['label'=>'New password'],'second_options'=>['label'=>'Repeat new password']])->getForm();
    }
}
