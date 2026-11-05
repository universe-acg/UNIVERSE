<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Controller\Dashboard\Organizer;

use App\Entity\File;
use App\Form\FileType;
use App\Form\FileTypeEdit;
use App\Service\AppServices;
use App\Service\FileService;
use Knp\Component\Pager\PaginatorInterface;
use App\Controller\BaseController as Controller;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\SecurityBundle\Security;

class FilesController extends Controller
{

    private $parameterBag;

    private $uploaderHelper;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    /**
     *
     */
#[Route("/management/files", name: "management_files", methods: ["GET"])]
    public function index(FileService $fileService, Request $request, PaginatorInterface $paginator, Security $security)
    {
        $files = $fileService->getFilesByUserId($this->getUser()->getId());

        $files = $paginator->paginate(
            $files,
            $request->query->getInt('page', 1),
            10,
            ['wrap-queries' => true]
        );

        return $this->render('Dashboard/Organizer/Files/index.html.twig', [
            'files' => $files,
            'managementClubsSocietiesService' => $fileService
        ]);
    }

    /**
     *
     */
#[Route("/management/files/download/{fileId}", name: "management_files_download_file")]
    public function download($fileId)
    {

        $file = $this->getDoctrine()->getRepository(File::class)->find($fileId);

        if (!$file) {
            throw $this->createNotFoundException('File not found');
        }

        $filePath = $this->parameterBag->get('kernel.project_dir') . "/public/uploads/files/" . $file->getFileName();

        $response = new BinaryFileResponse($filePath);

        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $file->getFileName() // Replace with your actual method to get the file name
        );

        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }

    public function getAbsolutePath(File $file)
    {
        return $this->uploaderHelper->asset($file, 'fileFile');
    }

    /**
     *
     */
#[Route("/management/files/add", name: "management_files_add", methods: ["GET","POST"])]
    public function add(Request $request, AppServices $services)
    {
        $em = $this->getDoctrine()->getManager();

        $file = new File();
        $user = $this->getUser();
        $file->setUser($user);
        $form = $this->createForm(FileType::class, $file);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $user = $this->getUser();
                $file->setUser($user);
                $em->persist($file);
                $em->flush();
                $this->addFlash('success', 'The file has been successfully uploaded');
                return $this->redirectToRoute("dashboard_organizer_management_files");
            }

            $this->addFlash('error', 'The form contains invalid data');
        }

        return $this->render('Dashboard/Organizer/Files/add.html.twig', [
            "form" => $form->createView(),
        ]);
    }

    /**
     *
     */
#[Route("/management/files/edit/{id}", name: "management_files_edit", methods: ["POST"])]
    public function edit(Request $request, $id, FileService $fileService)
    {
        $em = $this->getDoctrine()->getManager();

        $file = $fileService->getFileById($id);
        $filePath = $this->getParameter('kernel.project_dir') . '/public/uploads/files/' . $file->getFileName();

        $form = $this->createForm(FileTypeEdit::class, $file);
        if (file_exists($filePath)) {
            $fileObject = new \Symfony\Component\HttpFoundation\File\File($filePath);
            $file->setFileFile($fileObject);
            $form->get('currentFile')->setData($file->getFileName());
        }

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $user = $this->getUser();
                $file->setUser($user);
                $file->setFileName($form->get('fileFile')->getData()->getClientOriginalName() ?? "");
                $file->setFileOriginalName($form->get('fileFile')->getData()->getClientOriginalName() ?? "");
                $em->persist($file);
                $em->flush();
                $this->addFlash('success', 'The file has been successfully updated');
                return $this->redirectToRoute("dashboard_organizer_management_files");
            }

            $this->addFlash('error', 'The form contains invalid data');
        }

        return $this->render('Dashboard/Organizer/Files/edit.html.twig', [
            "form" => $form->createView(),
        ]);
    }

    /**
     *
     */
#[Route("/management/files/delete/{id}", name: "management_files_delete", methods: ["POST"])]
    public function delete($id, FileService $fileService, Filesystem $filesystem)
    {
        $em = $this->getDoctrine()->getManager();
        $file = $fileService->getFileById($id);

        if ($file) {

            $filePath = $this->getParameter('kernel.project_dir') . '/public/uploads/files/' . $file->getFileName();

            if (file_exists($filePath)) {
                unlink($filePath);
            }

            $em->remove($file);
            $em->flush();
            $this->addFlash('success', 'The file has been successfully deleted');
            return $this->redirectToRoute("dashboard_organizer_management_files");
        }

        $this->addFlash('error', 'File does not exist');
        return $this->redirectToRoute("dashboard_organizer_management_files");
    }
}
