<?php

namespace App\Command;

use App\Entity\Photo;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class DisablePhotosVisibilityCommand extends Command
{
    protected static $defaultName = 'app:photo-visible-false';
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('UStaiwa wszystkie zdjęcia jako prywatne dla ID uzytkownika')
            ->addArgument('user', InputArgument::REQUIRED, "Id uzytkownika wymagane");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->entityManager;

        $photoRepositry = $em->getRepository(Photo::class);

        $photoToSetPrivate = $photoRepositry->findBy(['is_public' => 1, 'user' => $input->getArgument('user')]);
        foreach ($photoToSetPrivate as $publicPhoto){
            $publicPhoto->setIsPublic(0);
            $em->persist($publicPhoto);
            $em->flush();
        }
        return 0;

    }

}