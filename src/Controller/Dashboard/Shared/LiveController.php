<?php
namespace App\Controller\Dashboard\Shared;
use App\Controller\BaseController;
use App\Entity\Live;
use App\Form\LiveType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\{Request,Response};
use Symfony\Component\Routing\Attribute\Route;
final class LiveController extends BaseController
{
    public function __construct(private EntityManagerInterface $em) {}
    #[Route('/organizer/manage-lives',name:'dashboard_organizer_live',methods:['GET'])]
    public function index(Request $request,PaginatorInterface $paginator): Response
    {
        $qb=$this->em->getRepository(Live::class)->createQueryBuilder('l')->where('l.owner=:owner')->setParameter('owner',$this->getUser())->orderBy('l.id','DESC');
        return $this->render('Dashboard/Shared/Live/index.html.twig',['lives'=>$paginator->paginate($qb,max(1,$request->query->getInt('page',1)),12)]);
    }
    #[Route('/organizer/manage-lives/add',name:'dashboard_organizer_live_add',methods:['GET','POST'])]
    #[Route('/organizer/manage-lives/{slug}/edit',name:'dashboard_organizer_live_edit',methods:['GET','POST'])]
    public function addedit(Request $request,?string $slug=null): Response
    {
        $live=$slug?$this->owned($slug):new Live(); if (!$slug) $live->setOwner($this->getUser());
        $form=$this->createForm(LiveType::class,$live); $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) { $this->em->persist($live); $this->em->flush(); $this->addFlash('success','Live session saved.'); return $this->redirectToRoute('dashboard_organizer_live'); }
        return $this->render('Dashboard/Shared/Live/add-edit.html.twig',['live'=>$live,'form'=>$form->createView()]);
    }
    #[Route('/organizer/manage-lives/{slug}/delete',name:'dashboard_organizer_live_delete',methods:['POST'])]
    #[Route('/organizer/manage-lives/{slug}/disable',name:'dashboard_organizer_live_disable',methods:['POST'])]
    public function delete(string $slug): Response { $this->owned($slug)->setDeletedAt(new \DateTime()); $this->em->flush(); return $this->redirectToRoute('dashboard_organizer_live'); }
    #[Route('/organizer/manage-lives/{slug}/restore',name:'dashboard_organizer_live_restore',methods:['POST'])]
    public function restore(string $slug): Response
    {
        $filters=$this->em->getFilters(); $enabled=$filters->isEnabled('softdeleteable'); if ($enabled) $filters->disable('softdeleteable');
        try { $live=$this->owned($slug); $live->setDeletedAt(null); $this->em->flush(); } finally { if ($enabled) $filters->enable('softdeleteable'); }
        return $this->redirectToRoute('dashboard_organizer_live');
    }
    #[Route('/organizer/manage-lives/{slug}/show',name:'dashboard_organizer_live_show',methods:['POST'])]
    #[Route('/organizer/manage-lives/{slug}/hide',name:'dashboard_organizer_live_hide',methods:['POST'])]
    public function showhide(string $slug,Request $request): Response
    {
        $live=$this->owned($slug); $live->setHidden(str_ends_with($request->getPathInfo(),'/hide')); $this->em->flush(); return $this->redirectToRoute('dashboard_organizer_live');
    }
    private function owned(string $slug): Live
    {
        $live=$this->em->getRepository(Live::class)->findOneBy(['slug'=>$slug]); if (!$live) throw $this->createNotFoundException();
        if (!$this->isGranted('ROLE_ADMINISTRATOR') && $live->getOwner()?->getId()!==$this->getUser()->getId()) throw $this->createAccessDeniedException(); return $live;
    }
}
