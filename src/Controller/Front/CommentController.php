<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Controller\Front;

use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    /**
     * @Route("/delete-comment/{id}", name="fos_comment_delete")
     */
    public function deleteComment(
        EntityManagerInterface $entityManager,
        CommentRepository      $commentRepository,
                               $id
    ): JsonResponse
    {
        // Load the comment entity based on the provided parameters
        $comment = $commentRepository->findById($id);

        // Check if the comment exists
        if ($comment) {
            // Remove the comment from the database
            $entityManager->remove($comment);
            $entityManager->flush();
            return new JsonResponse(['message' => 'Comment deleted successfully'], 200);
        }

        return new JsonResponse(['message' => 'Unable to delete comment'], 400);
    }
}