<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */

    namespace App\Controller\Dashboard\Shared;

    use App\Entity\CV;
    use App\Entity\DocDocumentDTO;
    use App\Entity\User;
    use App\Form\CVType;
    use App\Form\DocDocumentsType;
    use App\Controller\BaseController as Controller;
    use Symfony\Component\HttpFoundation\BinaryFileResponse;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\HttpFoundation\ResponseHeaderBag;
    use Symfony\Component\Routing\Attribute\Route;

    class DocumentController extends Controller
    {
        /**
         *
         */
#[Route("/download-cv/{userId}", name: "download_cv")]
        public function downloadCVAction($userId)
        {
            $user = $this->getDoctrine()->getRepository(User::class)->find($userId);

            if (!$user) {
                throw $this->createNotFoundException('User not found');
            }

            $this->denyAccessUnlessGranted('ROLE_USER');
            if (!$this->isGranted('ROLE_ADMINISTRATOR') && $this->getUser()->getId() !== $user->getId()) throw $this->createAccessDeniedException();
            $cv = $user->getCV();

            if (!$cv) {
                throw $this->createNotFoundException('CV not found');
            }

            $filePath = $this->getParameter('kernel.project_dir') . '/var/private/cvfiles/' . basename($cv->getFileName());

            if (!is_file($filePath)) throw $this->createNotFoundException('File not found');
            $response = new BinaryFileResponse($filePath);

            $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $cv->getFileOriginalName());

            return $response;
        }

        public function editDoc(Request $request): Response
        {
            $user = $this->getUser(); // Assuming you have a method to get the authenticated user

            $this->denyAccessUnlessGranted('ROLE_USER');
            if (!$this->isGranted('ROLE_ADMINISTRATOR') && $this->getUser()->getId() !== $user->getId()) throw $this->createAccessDeniedException();
            $cv = $user->getCV();
            $doc = $user->getDoc();

            if (!$cv) {
                $cv = new CV();
                $cv->setUser($user);
            }

            if (!$doc) {
                $doc = new DocDocumentDTO();
                $doc->setUser($user);
            }

            $form = $this->createForm(CVType::class, $cv);
            $formdoc = $this->createForm(DocDocumentsType::class, $doc);

            $form->handleRequest($request);
            $formdoc->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $deleteCvfile = $form->get('deleteCvfile')->getData();

                if ($deleteCvfile) {
                    $entityManager->remove($cv);
                } else {
                    $cv->setFileName($cv->getCvfile()->getClientOriginalName());
                    $cv->setFileMimeType($cv->getCvfile()->getClientMimeType());
                    $cv->setFileSize($cv->getCvfile()->getSize());
                    $cv->setFileOriginalName($cv->getCvfile()->getClientOriginalName());
                    $entityManager->persist($cv);
                }
                $entityManager->flush();
                $this->addFlash('success', 'CV updated successfully.');

                return $this->redirectToRoute('dashboard_doc_edit');
            }

            if ($formdoc->isSubmitted() && $formdoc->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();

                $deletedocfile1 = $formdoc->get('deletedocfile1')->getData();
                $deletedocfile2 = $formdoc->get('deletedocfile2')->getData();

                if ($deletedocfile1) {
                    $entityManager->remove($doc->getDocfile1());
                } elseif ($deletedocfile2) {
                    $entityManager->remove($doc->getDocfile2());
                } else {
                    $doc->setFileName($doc->getDocfile1()->getClientOriginalName());
                    $doc->setFileMimeType($doc->getDocfile1()->getClientMimeType());
                    $doc->setFileSize($doc->getDocfile1()->getSize());
                    $doc->setFileOriginalName($doc->getDocfile1()->getClientOriginalName());

                    $doc->setFileName1($doc->getDocfile2()->getClientOriginalName());
                    $doc->setFileMimeType1($doc->getDocfile2()->getClientMimeType());
                    $doc->setFileSize1($doc->getDocfile2()->getSize());
                    $doc->setFileOriginalName1($doc->getDocfile2()->getClientOriginalName());

                    $user->setDoc($doc);
                    $doc->setUser($user);
                    $entityManager->persist($user);
                }

                $entityManager->flush();
                $this->addFlash('success', 'Document(s) updated successfully.');

                return $this->redirectToRoute('dashboard_doc_edit');
            }

            return $this->render('Dashboard/Shared/CV/add-edit.html.twig', array(
                'form' => $form->createView(),
                'formdoc1' => $formdoc->createView(),
                'cv' => $cv,
            ));
        }

        public function deleteDoc(Request $request): Response
        {
            $user = $this->getUser();

            $this->denyAccessUnlessGranted('ROLE_USER');
            if (!$this->isGranted('ROLE_ADMINISTRATOR') && $this->getUser()->getId() !== $user->getId()) throw $this->createAccessDeniedException();
            $cv = $user->getCV();

            if ($cv) {

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($cv);
                $entityManager->flush();

                $this->addFlash('success', 'CV deleted successfully.');
            }

            return $this->redirectToRoute('dashboard_doc_edit');
        }
    }
