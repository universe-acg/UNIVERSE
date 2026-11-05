<?php
namespace App\Controller\Api;
use App\Controller\BaseController;
use Symfony\Component\HttpFoundation\{Request,JsonResponse};
use Symfony\Component\Routing\Attribute\Route;
final class TwillioController extends BaseController
{
    #[Route('/twillio', name:'twillio_route', methods: ["POST"])]
    public function captureSMS(Request $request, \App\Service\TwilioService $sms): JsonResponse {
        $this->denyAccessUnlessGranted('ROLE_ADMINISTRATOR');
        if(!$this->isCsrfTokenValid('action',$request->request->get('_action_token')))throw $this->createAccessDeniedException();
        $to=trim((string)$request->request->get('to'));$message=trim((string)$request->request->get('message'));
        if(!preg_match('/^\\+[1-9][0-9]{7,14}$/',$to)||$message===''||mb_strlen($message)>1600)return $this->json(['error'=>'Enter a valid recipient and message.'],422);
        return $this->json($sms->sendSMS($to,$message));
    }
}
