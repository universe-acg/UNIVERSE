<?php
namespace App\Controller\Front;
use App\Controller\BaseController;
use App\Entity\{Reel,ReelLike};
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
final class ReelsLikesController extends BaseController
{
    public function __construct(private EntityManagerInterface $em) {}
    public function getLikesOfUser(int $userId): JsonResponse
    {
        $this->owner($userId);$likes=$this->em->getRepository(ReelLike::class)->findBy(['user'=>$userId]);
        return $this->json(array_values(array_filter(array_map(static fn($like)=>$like->getReel()&&!$like->getReel()->getHidden()&&!$like->getReel()->getDeletedAt()?['reelId'=>$like->getReel()->getId()]:null,$likes))));
    }
    public function hasUserLikedReel(int $userId,int $reelId): JsonResponse { $this->owner($userId);$this->visible($reelId);return $this->json(['hasLiked'=>$this->em->getRepository(ReelLike::class)->findOneBy(['user'=>$userId,'reel'=>$reelId])!==null]); }
    public function getLikeOfUserForReel(int $userId,int $reelId): JsonResponse { return $this->hasUserLikedReel($userId,$reelId); }
    public function getLikeCountForReel(int $reelId): JsonResponse { $this->visible($reelId);return $this->json(['likeCount'=>$this->em->getRepository(ReelLike::class)->count(['reel'=>$reelId])]); }
    public function getUsersWhoLikedReel(int $reelId): JsonResponse
    {
        $this->visible($reelId);$users=[];foreach($this->em->getRepository(ReelLike::class)->findBy(['reel'=>$reelId]) as $like){$user=$like->getUser();if($user&&$user->isEnabled()&&!$user->getDeletedAt())$users[]=['id'=>$user->getId(),'name'=>$user->getCrossRoleName()];}return $this->json($users);
    }
    private function owner(int $id): void { if(!$this->getUser()||($this->getUser()->getId()!==$id&&!$this->isGranted('ROLE_ADMINISTRATOR')))throw $this->createAccessDeniedException(); }
    private function visible(int $id): void { if(!$this->em->getRepository(Reel::class)->findOneBy(['id'=>$id,'hidden'=>false,'deletedAt'=>null]))throw $this->createNotFoundException(); }
}
