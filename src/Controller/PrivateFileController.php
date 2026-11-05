<?php
namespace App\Controller;
use App\Entity as E;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Routing\Attribute\Route;
/** Existing upload URLs are retained, with authenticated delivery outside the public directory. */
final class PrivateFileController extends BaseController
{
    #[Route('/uploads/{folder}/{filename}',name:'private_upload',requirements:['folder'=>'cvfiles|doc_file1|doc_file2|files|applications'],methods:['GET'])]
    public function download(string $folder,string $filename,EntityManagerInterface $em): BinaryFileResponse {
        $this->denyAccessUnlessGranted('ROLE_USER');
        if($filename!==basename($filename))throw $this->createNotFoundException();
        $types=match($folder){'cvfiles'=>[[E\CV::class,'fileName']],'doc_file1'=>[[E\DocDocumentDTO::class,'fileName'],[E\DocDocument1::class,'fileName']],'doc_file2'=>[[E\DocDocumentDTO::class,'fileName1'],[E\DocDocument2::class,'fileName']],'applications'=>[[E\ListingSubmission::class,'fileName']],default=>[[E\File::class,'fileName'],[E\ListingSubmission::class,'fileName']]};
        $allowed=false;
        foreach($types as [$class,$field]){
            $criteria=[$field=>$filename];if(!$this->isGranted('ROLE_ADMINISTRATOR'))$criteria['user']=$this->getUser();
            if($em->getRepository($class)->findOneBy($criteria)){$allowed=true;break;}
        }
        if(!$allowed)throw $this->createNotFoundException();
        $root=$this->getParameter('kernel.project_dir').'/var/private/'.$folder;
        $path=realpath($root.'/'.$filename);
        if(!$path||!str_starts_with($path,$root.'/')||!is_file($path))throw $this->createNotFoundException();
        return $this->file($path,$filename);
    }
}
