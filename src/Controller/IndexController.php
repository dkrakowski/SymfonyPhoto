<?php

namespace App\Controller;

use App\Entity\Photo;
use App\Form\UploadPhotoType;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request)
    {
        $form = $this->createForm(UploadPhotoType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();

            if ($this->getUser()){
                $photoFileName = $form->get('filename')->getData();
                if($photoFileName) {
                    try {
                        $originalFileName = pathinfo($photoFileName->getClientOriginalName(), PATHINFO_FILENAME);
                        $safeFileName = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z)-9_] remove; Lower()', $originalFileName);
                        $newFileNAme = $safeFileName.'-'.uniqid().'-'.$photoFileName->guessExtension();
                        $photoFileName->move('images/hosting', $newFileNAme);

                        $entityPhoto = new Photo();
                        $entityPhoto->setFilename($newFileNAme);
                        $entityPhoto->setIsPublic($form->get('is_public')->getData());
                        $entityPhoto->setUploadedAt(new \DateTime());
                        $entityPhoto->setUser($this->getUser());

                        $em->persist($entityPhoto);
                        $em->flush();
                        $this->addFlash('success','Dodano zdjęcie');

                    }catch (\Exception $e){
                        $this->addFlash('error','Wsytąpił błąd');
                    }


                }else {
                    $this->addFlash('error','Wsytąpił błąd');
                }

            }
        }
        return $this->render('index/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
