<?php
namespace App\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
final class AuthController extends BaseController
{
    #[Route('/signin', name: 'fos_user_security_login', methods: ['GET'])]
    public function login(AuthenticationUtils $authentication): Response {
        if ($this->getUser()) return $this->redirectToRoute('dashboard_index');
        return $this->render('bundles/FOSUserBundle/Security/login.html.twig', ['last_username'=>$authentication->getLastUsername(), 'error'=>$authentication->getLastAuthenticationError(), 'csrf_token'=>$this->container->get('security.csrf.token_manager')->getToken('authenticate')->getValue()]);
    }
    #[Route('/login_check', name: 'fos_user_security_check', methods: ['POST'])]
    public function check(): never { throw new \LogicException('The security firewall handles login.'); }
    #[Route('/logout', name: 'fos_user_security_logout', methods: ['POST'])]
    public function logout(): never { throw new \LogicException('The security firewall handles logout.'); }
    #[Route('/health', name: 'app_health', methods: ['GET'])]
    public function health(): Response { return $this->json(['status'=>'ok', 'application'=>'UNIVERSE']); }
}
