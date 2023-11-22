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
    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\HttpFoundation\BinaryFileResponse;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\HttpFoundation\ResponseHeaderBag;
    use Symfony\Component\Routing\Annotation\Route;
    
    class DocumentController extends Controller
    {
        /**
         * @Route("/download-cv/{userId}", name="download_cv")
         */
        public function downloadCVAction($userId)
        {
            $user = $this->getDoctrine()->getRepository(User::class)->find($userId);
            
            if (!$user) {
                throw $this->createNotFoundException('User not found');
            }
            
            $cv = $user->getCV();
            
            if (!$cv) {
                throw $this->createNotFoundException('CV not found');
            }
            
            // Get the file path
            $filePath = $this->getParameter('kernel.project_dir') . '/public/uploads/files/' . $cv->getFileName();
            
            // Create a BinaryFileResponse to handle the file download
            $response = new BinaryFileResponse($filePath);
            
            // Set the file name in the response headers
            $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $cv->getFileOriginalName());
            
            return $response;
        }
        
        public function editDoc(Request $request): Response
        {
            $user = $this->getUser(); // Assuming you have a method to get the authenticated user
            
            // Fetch the CV entity for the user
            $cv = $user->getCV();
            $doc = $user->getDoc();
            
            // If the user doesn't have a CV, create a new CV entity
            if (!$cv) {
                $cv = new CV();
                $cv->setUser($user);
            }
            
            // If the user doesn't have a CV, create a new CV entity
            if (!$doc) {
                $doc = new DocDocumentDTO();
                $doc->setUser($user);
            }
            
            // Create the CV form
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
                
                
                // Redirect to the CV edit page or any other appropriate page
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
                
                
                // Redirect to the CV edit page or any other appropriate page
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
            
            // Fetch the CV entity for the user
            $cv = $user->getCV();
            
            if ($cv) {
                // Delete the CV entity
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($cv);
                $entityManager->flush();
                
                $this->addFlash('success', 'CV deleted successfully.');
            }
            
            // Redirect to the CV edit page or any other appropriate page
            return $this->redirectToRoute('dashboard_doc_edit');
        }
    }
