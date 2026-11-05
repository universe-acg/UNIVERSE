<?php
namespace App\Controller\Front;
use App\Controller\BaseController;
use App\Entity\{Reel,ReelLike};
use App\Repository\ReelRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\{Request,Response,JsonResponse};
use Symfony\Component\Routing\Attribute\Route;
final class ReelsController extends BaseController
{
    private const PAGE_SIZE=24;
    public function __construct(private ReelRepository $reels,private EntityManagerInterface $em) {}
    private function page(Request $request,PaginatorInterface $paginator)
    {
        $keyword=trim((string)$request->query->get('keyword',''));
        return $paginator->paginate($this->reels->getReelssql(false,$keyword?:'all','all','all',false),max(1,$request->query->getInt('page',1)),self::PAGE_SIZE);
    }
    #[Route('/reels',name:'reels',methods:['GET'])]
    public function reels(Request $request,PaginatorInterface $paginator): Response
    {
        $reels=$this->page($request,$paginator); return $this->render('Front/Reels/reels.html.twig',['reels'=>$reels,'hasMore'=>$reels->getCurrentPageNumber()*self::PAGE_SIZE<$reels->getTotalItemCount()]);
    }
    #[Route('/load-more-reels',name:'load_more_reels',methods:['GET'])]
    public function loadMoreReels(Request $request,PaginatorInterface $paginator): JsonResponse
    {
        $reels=$this->page($request,$paginator); $page=$reels->getCurrentPageNumber(); $total=(int)ceil($reels->getTotalItemCount()/self::PAGE_SIZE);
        return $this->json(['html'=>$this->renderView('Front/Reels/partial-reel.html.twig',['reels'=>$reels]),'page'=>$page,'totalPages'=>$total,'nextPage'=>$page<$total?$page+1:null,'total'=>$reels->getTotalItemCount()]);
    }
    #[Route('/reel/{slug}',name:'reel',methods:['GET'])]
    public function reelBySlug(string $slug): Response
    {
        $reel=$this->visible($slug); $reel->viewed(); $this->em->flush();
        return $this->render('Front/Reels/reels_details.html.twig',['reel'=>$reel,'likeCount'=>$this->em->getRepository(ReelLike::class)->count(['reel'=>$reel]),'liked'=>$this->getUser() && $this->em->getRepository(ReelLike::class)->findOneBy(['reel'=>$reel,'user'=>$this->getUser()])!==null]);
    }
    #[Route('/reel/{slug}/like',name:'reel_set_like',methods:['POST'])]
    public function setLike(string $slug,Request $request): JsonResponse
    {
        if (!$this->getUser()) return $this->json(['error'=>'Sign in to like a reel.'],401);
        if (!$this->isCsrfTokenValid('action',$request->headers->get('X-CSRF-Token')??$request->getPayload()->get('_action_token'))) throw $this->createAccessDeniedException();
        $reel=$this->visible($slug); $liked=$request->getPayload()->getBoolean('liked',true); $db=$this->em->getConnection();
        if ($liked) $db->executeStatement('INSERT IGNORE INTO universe_reels_likes (user_id,reel_id) VALUES (?,?)',[$this->getUser()->getId(),$reel->getId()]);
        else $db->executeStatement('DELETE FROM universe_reels_likes WHERE user_id=? AND reel_id=?',[$this->getUser()->getId(),$reel->getId()]);
        return $this->json(['liked'=>$liked,'count'=>(int)$db->fetchOne('SELECT COUNT(*) FROM universe_reels_likes WHERE reel_id=?',[$reel->getId()])]);
    }
    private function visible(string $slug): Reel
    {
        $reel=$this->reels->findOneBy(['slug'=>$slug,'hidden'=>false,'deletedAt'=>null]); if (!$reel) throw $this->createNotFoundException('Reel not found.'); return $reel;
    }
}
