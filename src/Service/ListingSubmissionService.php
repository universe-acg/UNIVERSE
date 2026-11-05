<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */

    namespace App\Service;

    use App\Entity\Listing;
    use App\Entity\ListingSubmission;
    use App\Repository\ListingSubmisionRepository;
    use App\Repository\UserRepository;
    use Doctrine\ORM\EntityManagerInterface;
    use App\Service\DemoMailer as Swift_Mailer;
    use Symfony\Component\Cache\Adapter\AdapterInterface;
    use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
    use Symfony\Component\HttpFoundation\RequestStack;
    use Symfony\Component\HttpKernel\KernelInterface;
    use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
    use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
    use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
    use Symfony\Bundle\SecurityBundle\Security;
    use Symfony\Component\Security\Core\User\UserInterface;
    use Twig\Environment;

    class ListingSubmissionService
    {
        private $em;
        private $authChecker;
        private $requestStack;
        private $kernel;
        private $cache;
        private $router;
        private $security;
        private $mailer;
        private $params;
        private $templating;
        private $urlMatcherInterface;
        private $userRepository;
        private $listingSubmisionRepository;

        public function __construct(EntityManagerInterface $entityManager, AuthorizationCheckerInterface $authChecker,
                                    RequestStack           $requestStack, KernelInterface $kernel, AdapterInterface $cache,
                                    UrlGeneratorInterface  $router, Security $security, Swift_Mailer $mailer,
                                    ParameterBagInterface  $params, \Twig\Environment $templating, UrlMatcherInterface $urlMatcherInterface,
                                    UserRepository         $userRepository, ListingSubmisionRepository $listingSubmisionRepository)
        {
            $this->userRepository = $userRepository;
            $this->em = $entityManager;
            $this->authChecker = $authChecker;
            $this->requestStack = $requestStack;
            $this->kernel = $kernel;
            $this->cache = $cache;
            $this->router = $router;
            $this->security = $security;
            $this->mailer = $mailer;
            $this->params = $params;
            $this->templating = $templating;
            $this->urlMatcherInterface = $urlMatcherInterface;
            $this->listingSubmisionRepository = $listingSubmisionRepository;
        }

        /**
         * Add a new ListingSubmission for the specified Listing and User.
         *
         * @param Listing $listing
         * @param UserInterface $user
         * @param string $content
         * @return ListingSubmission
         */
        public function add(Listing $listing, UserInterface $user, string $content): ListingSubmission
        {
            $listingSubmission = new ListingSubmission();
            $listingSubmission->setListing($listing);
            $listingSubmission->setUser($user);
            $listingSubmission->setContent($content);

            $this->em->persist($listingSubmission);
            $this->em->flush();

            return $listingSubmission;
        }

        /**
         * Check if a ListingSubmission exists for the specified Listing and User.
         *
         * @param Listing $listing
         * @param UserInterface $user
         * @return bool
         */
        public function checkIfExists(Listing $listing, UserInterface $user): bool
        {
            $listingSubmission = $this->listingSubmisionRepository->findOneBy(['listing' => $listing, 'user' => $user]);

            return ($listingSubmission !== null);
        }

        /**
         * Get all ListingSubmissions.
         *
         * @return array|ListingSubmission[]
         */
        public function getAllSubmissions(): array
        {
            return $this->listingSubmisionRepository->findAll();
        }

        /**
         * Get ListingSubmissions by User ID.
         *
         * @param UserInterface $user
         * @return array|ListingSubmission[]
         */
        public function getSubmissionsByUserId(UserInterface $user): array
        {
            return $this->listingSubmisionRepository->findBy(['user' => $user]);
        }

        public function findDeletedSubmissionsByUser($user, EntityManagerInterface $entityManager)
        {
            $qb = $entityManager->createQueryBuilder();

            $query = $qb->select('ls')
                ->from(ListingSubmission::class, 'ls') // Replace with the actual namespace and entity name
                ->where('ls.user = :user')
                ->andWhere('ls.deletedAt IS NULL')
                ->setParameter('user', $user)
                ->getQuery();

            return $query->getResult();
        }

        public function findActiveSubmissionsByUser($user, EntityManagerInterface $entityManager)
        {
            $qb = $entityManager->createQueryBuilder();

            $query = $qb->select('ls')
                ->from(ListingSubmission::class, 'ls') // Replace with the actual namespace and entity name
                ->where('ls.user = :user')
                ->andWhere('ls.deletedAt is NULL')
                ->setParameter('user', $user)
                ->getQuery();

            return $query->getResult();
        }

        public function findActiveSubmissionsByEventId($eventId, EntityManagerInterface $entityManager)
        {
            $qb = $entityManager->createQueryBuilder();

            $query = $qb->select('ls')
                ->from(ListingSubmission::class, 'ls') // Replace with the actual namespace and entity name
                ->join('ls.listing', 'l')
                ->where('l.event = :eventId') // Assuming 'event' is the property name in the Listing entity that refers to the event
                ->andWhere('ls.deletedAt is NULL')
                ->setParameter('eventId', $eventId)
                ->getQuery();

            return $query->getResult();
        }

        public function findActiveSubmissionsByListingId($listingId, EntityManagerInterface $entityManager)
        {
            $qb = $entityManager->createQueryBuilder();

            $query = $qb->select('ls')
                ->from(ListingSubmission::class, 'ls') // Replace with the actual namespace and entity name
                ->where('ls.listing = :listingId') // Assuming 'listing' is the property name in the ListingSubmission entity that refers to the listing
                ->andWhere('ls.deletedAt is NULL')
                ->setParameter('listingId', $listingId)
                ->getQuery();

            return $query->getResult();
        }

        /**
         *
         */
        public function getSubmissionsBySlug($slug)
        {
            return $this->listingSubmisionRepository->findBy(['slug' => $slug]);
        }

        /**
         *
         */
        public function getSubmissionsById($id)
        {
            return $this->listingSubmisionRepository->findBy(['id' => $id]);
        }

        /**
         * Get ListingSubmissions by Listing.
         *
         * @param Listing $listing
         * @return array|ListingSubmission[]
         */
        public function getSubmissionsByListing(Listing $listing): array
        {
            return $this->listingSubmisionRepository->findBy(['listing' => $listing]);
        }

        /**
         * Delete a ListingSubmission by ID.
         *
         * @param int $submissionId
         */
        public function deleteSubmission(int $submissionId): void
        {
            $listingSubmission = $this->listingSubmisionRepository->find($submissionId);

            if ($listingSubmission) {
                $this->em->remove($listingSubmission);
                $this->em->flush();
            }
        }

        /**
         * Remove the ListingSubmission for the specified Listing and User.
         *
         * @param Listing $listing
         * @param UserInterface $user
         */
        public function remove(Listing $listing, UserInterface $user): void
        {
            $listingSubmission = $this->listingSubmisionRepository->findOneBy(['listing' => $listing, 'user' => $user]);

            if ($listingSubmission) {
                $this->em->remove($listingSubmission);
                $this->em->flush();
            }
        }

        /**
         * Get ListingSubmission by ID.
         *
         * @param int $submissionId
         * @return ListingSubmission|null
         */
        public function getSubmissionById(int $submissionId): ?ListingSubmission
        {
            return $this->listingSubmisionRepository->find($submissionId);
        }
    }
