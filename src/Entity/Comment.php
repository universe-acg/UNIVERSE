<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */
    
    namespace App\Entity;
    
    use App\Repository\VoteRepository;
    use Doctrine\ORM\Mapping as ORM;
    use FOS\CommentBundle\Entity\Comment as BaseComment;
    use FOS\CommentBundle\Model\SignedCommentInterface;
    use FOS\CommentBundle\Model\ThreadInterface as ThreadInterface;
    use FOS\CommentBundle\Model\VotableCommentInterface;
    use Symfony\Component\Security\Core\User\UserInterface;
    
    /**
     * @ORM\Entity(repositoryClass="App\Repository\CommentRepository")
     * @ORM\Table(name="universe_comment")
     * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
     */
    class Comment extends BaseComment implements SignedCommentInterface, VotableCommentInterface
    {
        
        
        /**
         * @ORM\Id
         * @ORM\Column(type="integer")
         * @ORM\GeneratedValue(strategy="AUTO")
         */
        protected $id;
        
        /**
         * Thread of this comment
         *
         * @var Thread
         * @ORM\ManyToOne(targetEntity="Thread")
         */
        protected $thread;
        
        /**
         * Author of the comment
         *
         * @ORM\ManyToOne(targetEntity="User")
         * @var User
         */
        protected $author;
        
        /**
         * @ORM\Column(type="integer")
         * @var int
         */
        protected $score = 0;
        
        private VoteRepository $voteRepository;
        
        public function Comment(VoteRepository $voteRepository)
        {
            $this->$voteRepository = $voteRepository;
        }
        
        public function getId()
        {
            return $this->id;
        }
        
        /**
         * @return ThreadInterface
         */
        public function getThread()
        {
            return $this->thread;
        }
        
        /**
         * @param ThreadInterface $thread
         *
         * @return void
         */
        public function setThread(ThreadInterface $thread)
        {
            $this->thread = $thread;
        }
        
        public function getAuthorName()
        {
            if (null === $this->getAuthor()) {
                return 'Anonymous';
            }
            
            return $this->getAuthor()->getUsername();
        }
        
        public function getAuthor()
        {
            return $this->author;
        }
        
        public function setAuthor(UserInterface $author)
        {
            $this->author = $author;
        }
        
        /**
         * Returns the current score of the comment.
         *
         * @return integer
         */
        public function getScore()
        {
            return $this->score;
        }
        
        /**
         * Sets the score of the comment.
         *
         * @param integer $score
         */
        public function setScore($score)
        {
            $this->score = $score;
        }
        
        /**
         * Increments the comment score by the provided
         * value.
         *
         * @param integer $by value
         * @return void The new comment score
         */
        public function incrementScore($by = 1)
        {
            $this->score += $by;
        }
        
        public function isUpvoteClickable($commentid)
        {
            $votes = $this->voteRepository->findVotesByCommentId($commentid);
            // votes of comment
            // if user id matches vote.voter.id
            // then return false;
        }
        
        public function isDownvoteClickable($userid, $commentid, $currentscore)
        {
            // votes of comment
            // if user id matches vote.voter.id
            // then return false;
        }
    }
