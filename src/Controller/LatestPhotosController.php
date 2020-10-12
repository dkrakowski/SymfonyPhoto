<?php

namespace App\Controller;

use App\Entity\Photo;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class LatestPhotosController extends AbstractController
{
    /**
     * @Route("/latest", name="latest_photos")
     */
    public function index(){
        $em = $this->getDoctrine()->getManager();
        $latestPHotosPublic = $em->getRepository(Photo::class)->findAllPublic();
//        $latestPHotosPublic = $em->getRepository(Photo::class)->findBy(['is_public'=>true]);

        return $this->render('latest_photo/latest_photo.html.twig',[
            'latestPhotosPublic' => $latestPHotosPublic
        ]);

    }
}