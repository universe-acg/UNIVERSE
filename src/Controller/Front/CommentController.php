<?php
namespace App\Controller\Front;
use App\Controller\BaseController;
use App\Entity\{Comment,Thread,Event,BlogPost};
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\{Request,Response,JsonResponse};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints as Assert;
final class CommentController extends BaseController
{
    public function __construct(private EntityManagerInterface $em) {}
    #[Route('/comments/threads',name:'fos_comment_get_threads',methods:['GET'])]
    public function threads(): Response {return $this->json([]);}
    #[Route('/comments/threads/{id}/comments',name:'fos_comment_get_thread_comments',methods:['GET'])]
    public function comments(string $id): Response {
        $thread=$this->thread($id);$all=$this->em->getRepository(Comment::class)->findBy(['thread'=>$thread],['createdAt'=>'ASC']);$byParent=[];
        foreach($all as $comment)$byParent[$comment->getParent()?->getId()??0][]=$comment;
        $tree=function(int $parent,int $depth=0)use(&$tree,$byParent): array {if($depth>30)return [];return array_map(fn($comment)=>['comment'=>$comment,'children'=>$tree($comment->getId(),$depth+1)],$byParent[$parent]??[]);};
        return $this->render('@FOSComment/Thread/comments.html.twig',['thread'=>$thread,'comments'=>$tree(0)]);
    }
    #[Route('/comments/threads/{id}/comments/new',name:'fos_comment_new_thread_comments',methods:['GET'])]
    public function newComment(string $id,Request $request): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');$thread=$this->thread($id);if(!$thread->isCommentable())throw $this->createAccessDeniedException();
        $parent=$request->query->getInt('parentId')?$this->comment($id,$request->query->getInt('parentId')):null;
        return $this->render('@FOSComment/Thread/comment_new.html.twig',['id'=>$id,'parent'=>$parent,'first'=>$thread->getNumComments()===0,'form'=>$this->commentForm(new Comment())->createView()]);
    }
    #[Route('/comments/threads/{id}/comments',name:'fos_comment_post_thread_comments',methods:['POST'])]
    public function postComment(string $id,Request $request): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');$thread=$this->thread($id);if(!$thread->isCommentable())throw $this->createAccessDeniedException();
        $comment=new Comment();$form=$this->commentForm($comment);$form->handleRequest($request);
        if(!$form->isSubmitted()||!$form->isValid())return $this->json(['error'=>'Enter a valid comment and try again.'],422);
        $comment->setThread($thread);$comment->setAuthor($this->getUser());$parent=$request->query->getInt('parentId');if($parent)$comment->setParent($this->comment($id,$parent));
        $this->em->persist($comment);$thread->setNumComments($thread->getNumComments()+1);$this->em->persist($thread);$this->em->flush();return $this->comments($id);
    }
    #[Route('/comments/threads/{id}/comments/{commentId}/edit',name:'fos_comment_edit_thread_comment',methods:['GET'])]
    public function editComment(string $id,int $commentId): Response {
        $comment=$this->comment($id,$commentId);$this->owner($comment);return $this->render('@FOSComment/Thread/comment_edit.html.twig',['comment'=>$comment,'form'=>$this->commentForm($comment)->createView()]);
    }
    #[Route('/comments/threads/{id}/comments/{commentId}',name:'fos_comment_put_thread_comments',methods:['POST'])]
    public function updateComment(string $id,int $commentId,Request $request): Response {
        $comment=$this->comment($id,$commentId);$this->owner($comment);$form=$this->commentForm($comment);$form->handleRequest($request);
        if(!$form->isSubmitted()||!$form->isValid())return $this->json(['error'=>'Invalid comment.'],422);
        $this->em->persist($comment);$this->em->flush();return $this->comments($id);
    }
    #[Route('/delete-comment/{id}',name:'fos_comment_delete',methods:['POST'])]
    public function deleteComment(int $id): JsonResponse {
        $comment=$this->em->find(Comment::class,$id);if(!$comment)throw $this->createNotFoundException();$this->owner($comment);
        if($comment->getState()!==1){$comment->setState(1);$comment->setBody('');$thread=$comment->getThread();$thread->setNumComments(max(0,$thread->getNumComments()-1));$this->em->persist($thread);$this->em->persist($comment);$this->em->flush();}
        return $this->json(['message'=>'Comment deleted.']);
    }
    #[Route('/comments/threads/{id}/commentable',name:'fos_comment_edit_thread_commentable',methods:['POST'])]
    public function toggleThread(string $id,Request $request): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMINISTRATOR');if(!$this->isCsrfTokenValid('action',$request->headers->get('X-CSRF-Token')))throw $this->createAccessDeniedException();
        $thread=$this->thread($id);$thread->setIsCommentable(!$thread->isCommentable());$this->em->persist($thread);$this->em->flush();return $this->comments($id);
    }
    private function commentForm(Comment $comment): \Symfony\Component\Form\FormInterface {return $this->createFormBuilder($comment,['csrf_token_id'=>'comment'])->add('body',TextareaType::class,['label'=>false,'constraints'=>[new Assert\NotBlank(),new Assert\Length(max:4000)]])->getForm();}
    private function owner(Comment $comment): void {if(!$this->isGranted('ROLE_ADMINISTRATOR')&&(!$this->getUser()||$comment->getAuthor()?->getId()!==$this->getUser()->getId()))throw $this->createAccessDeniedException();}
    private function comment(string $id,int $commentId): Comment {$comment=$this->em->getRepository(Comment::class)->findOneBy(['id'=>$commentId,'thread'=>$id]);if(!$comment)throw $this->createNotFoundException();return $comment;}
    private function thread(string $id): Thread {
        $thread=$this->em->find(Thread::class,$id);if($thread)return $thread;
        $event=$this->em->getRepository(Event::class)->findOneBy(['slug'=>$id,'published'=>true]);$blog=$this->em->getRepository(BlogPost::class)->findOneBy(['slug'=>$id]);
        if(!$event&&!$blog)throw $this->createNotFoundException();
        $thread=new Thread();$thread->setId($id);$thread->setPermalink($event?'/event/'.$id:'/blog/'.$id);$this->em->persist($thread);$this->em->flush();return $thread;
    }
}
