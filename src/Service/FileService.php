<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */

    namespace App\Service;

    use App\Entity\File;
    use App\Entity\User;
    use App\Repository\FileRepository;
    use Doctrine\ORM\EntityManagerInterface;
    use App\Service\DemoMailer as Swift_Mailer;
    use Symfony\Component\Cache\Adapter\AdapterInterface;
    use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
    use Symfony\Component\HttpFoundation\File\UploadedFile;
    use Symfony\Component\HttpFoundation\RequestStack;
    use Symfony\Component\HttpKernel\KernelInterface;
    use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
    use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
    use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
    use Symfony\Bundle\SecurityBundle\Security;
    use Symfony\Component\String\Slugger\SluggerInterface;
    use Twig\Environment;

    class FileService
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

        private $fileRepository;

        private $userService;

        public function __construct(UserService $userService, EntityManagerInterface $entityManager, AuthorizationCheckerInterface $authChecker, RequestStack $requestStack, KernelInterface $kernel, AdapterInterface $cache, UrlGeneratorInterface $router, Security $security, Swift_Mailer $mailer, ParameterBagInterface $params, \Twig\Environment $templating, UrlMatcherInterface $urlMatcherInterface, FileRepository $fileRepository)
        {
            $this->fileRepository = $fileRepository;
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
            $this->userService = $userService;
        }

        public function getFilesByUserId(int $userId)
        {
            return $this->fileRepository->getFilesByUserId($userId);
        }

        public function updateFile(int $fileId, array $data)
        {
            $file = $this->fileRepository->find($fileId);

            if (!$file) {
                return null; // File not found
            }

            // Update file properties based on the $data array
            foreach ($data as $key => $value) {
                switch ($key) {
                    case 'fileName':
                        $file->setFileName($value);
                        break;
                    case 'fileSize':
                        $file->setFileSize($value);
                        break;
                    // Add more cases for other properties as needed
                }
            }

            $this->em->persist($file);
            $this->em->flush();

            return $file; // Return the updated file entity
        }

        public function deleteFile(int $fileId)
        {
            $file = $this->fileRepository->find($fileId);

            if (!$file) {
                return false; // File not found
            }

            $this->em->remove($file);
            $this->em->flush();

            return true; // File successfully deleted
        }

        public function getAllFiles()
        {
            return $this->fileRepository->findAll();
        }

        public function getFileById(int $fileId): ?File
        {
            return $this->fileRepository->getFileById($fileId);
        }

        public function uploadFile(UploadedFile $file, User $user, string $uploadDir, SluggerInterface $slugger)
        {
            // Generate a unique file name to avoid overwriting existing files
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $fileName = $safeFilename . '-' . uniqid('', true) . '.' . $file->guessExtension();

            // Move the uploaded file to the specified directory
            $file->move($uploadDir, $fileName);

            // Create a new File entity and set its properties
            $newFile = new File();
            $newFile->setUser($user);
            $newFile->setFileName($fileName);
            $newFile->setFileSize($file->getSize());
            $newFile->setFileMimeType($file->getMimeType());
            $newFile->setFileOriginalName($originalFilename);

            // You can set other properties like file dimensions if needed

            // Persist the new file entity
            $this->em->persist($newFile);
            $this->em->flush();

            return $newFile; // Return the newly created file entity
        }

        public function getFileContents(int $fileId, string $downloadDir)
        {
            $filePath = $this->downloadFile($fileId, $downloadDir);

            if (!$filePath) {
                return null; // File not found
            }

            // Read and return the file contents
            return file_get_contents($filePath);
        }

        public function downloadFile(int $fileId, string $downloadDir)
        {
            $file = $this->fileRepository->find($fileId);

            if (!$file) {
                return null; // File not found
            }

            // Generate the file path
            return $downloadDir . '/' . $file->getFileName(); // Return the file path for download
        }

    }
