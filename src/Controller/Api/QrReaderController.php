<?php
namespace App\Controller\Api;
use App\Controller\BaseController;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\{Request,Response};
use Symfony\Component\Routing\Attribute\Route;
final class QrReaderController extends BaseController
{
    #[Route('/scanqr',name:'qr_reader',methods:['GET'])]
    public function qrReader(): Response {
        if(!$this->isGranted('ROLE_ORGANIZER')&&!$this->isGranted('ROLE_ADMINISTRATOR'))throw $this->createAccessDeniedException();
        return $this->render('Front/Page/qr_reader.html.twig');
    }
    #[Route('/api/handle-post-request',name:'qr_check_in', methods: ["POST"])]
    public function handlePostRequest(Request $request,EntityManagerInterface $em): Response {
        if(!$this->isGranted('ROLE_ORGANIZER')&&!$this->isGranted('ROLE_ADMINISTRATOR'))throw $this->createAccessDeniedException();
        if(!$this->isCsrfTokenValid('action',$request->headers->get('X-CSRF-Token')))throw $this->createAccessDeniedException();
        try{$data=$request->toArray();}catch(\Throwable){return $this->json(400,400);}
        $code=(string)($data['qrdata']??'');if(!preg_match('~^([0-9]+)/([A-Za-z0-9_-]+)$~',$code,$match))return $this->json(400,400);
        $order=$em->getRepository(Order::class)->findOneBy(['id'=>(int)$match[1],'reference'=>$match[2]]);if(!$order)return $this->json(404,404);
        if(!$this->isGranted('ROLE_ADMINISTRATOR'))foreach($order->getOrderelements() as $element)if($element->getEventticket()->getEventdate()->getEvent()->getOrganizer()?->getUser()?->getId()!==$this->getUser()->getId())throw $this->createAccessDeniedException();
        if(!in_array($order->getStatus(),[1,2],true))return $this->json(409,409);
        if($order->getStatus()!==2){$order->setStatus(2);$em->persist($order);$em->flush();}
        return $this->json(200);
    }
}
