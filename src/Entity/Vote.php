<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Model\Vote as BaseVote;


use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Table(name: 'universe_vote')]
#[ORM\Entity(repositoryClass: \App\Repository\VoteRepository::class)]
#[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
class Vote extends BaseVote
{

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    protected $id;

    /**
     * Comment of this vote
     *
     * @var Comment
     */
    #[ORM\ManyToOne(targetEntity: \App\Entity\Comment::class)]
    protected $comment;

    /**
     * Author of the vote
     *
     * @var User
     */
    #[ORM\ManyToOne(targetEntity: \App\Entity\User::class)]
    protected $voter;

    public function getId()
    {
        return $this->id;
    }

    /**
     * Gets the owner of the vote
     *
     * @return UserInterface
     */
    public function getVoter()
    {
        return $this->voter;
    }

    /**
     * Sets the owner of the vote
     *
     * @param UserInterface $voter
     */
    public function setVoter(UserInterface $voter)
    {
        $this->voter = $voter;
    }

    public function getComment()
    {
        return $this->comment;
    }

    public function setComment(Comment $comment)
    {
        $this->comment = $comment;

        return $this;
    }

}
