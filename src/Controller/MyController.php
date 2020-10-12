<?php

namespace App\Controller;

use App\Entity\Photo;
use App\Service\PhotoVisibilityService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\MakerBundle\FileManager;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class MyController
 * @package App\Controller
 * @IsGranted("ROLE_USER")
 */
class MyController extends AbstractController
{
    /**
     * @Route("/my/photos", name="my_photos")
     */
    public function index()
    {
        $em = $this->getDoctrine()->getManager();
        $myPhotos = $em->getRepository(Photo::class)->findBy(['user'=>$this->getUser()]);


        return $this->render('my/my_photo.html.twig',[
            'myPhotos' => $myPhotos
        ]);

    }

    /**
     * @Route("/my/photos/set_visbility/{id}/{visibility}", name="my_photos_set_visbility")
     * @param PhotoVisibilityService $photoVisibilityService
     * @param int $id
     * @param bool $visibility
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function myPhotosVisiblity(PhotoVisibilityService $photoVisibilityService, int $id, bool $visibility)
    {
        $message = [
            '1' => 'publiczne',
            '0' =>  'prywatne'
        ];

     if ($photoVisibilityService->makeVisible($id, $visibility)){
         $this->addFlash('success', "Ustawiono jako ".$message[$visibility]);
     } else {
         $this->addFlash('error', "Błąd w czasie ustaiwania jako ".$message[$visibility]);
     }

     return $this->redirectToRoute('my_photos');
    }

    /**
     * @Route("/my/photos/remove/{id}", name="my_photos_remove")
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function myPhotoRemove(int $id){
        $em = $this->getDoctrine()->getManager();
        $myPhoto = $em->getRepository(Photo::class)->find($id);

            if ($this->getUser() == $myPhoto->getUser()) {
                $em->remove($myPhoto);
                $em->flush();
                $this->addFlash('succes', "Usunięto zdjęcie");
            } else {
                $this->addFlash('error', "Nie możesz usunąć zdjęcia innego użytkownika");
            }

        return $this->redirectToRoute('my_photos');

    }
}
