<?php

namespace App\Controller;

use App\Entity\Serie;
use App\Repository\SerieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

//attribut de la class qui permet de mutualiser des informations
#[Route('/serie', name: 'serie_')]
class SerieController extends AbstractController
{
    #[Route('/list', name: 'list', methods: "GET")]
    public function list(SerieRepository $serieRepository): Response
    {
//        $series = $serieRepository -> findAll();
//        dump($series);
//        $series = $serieRepository->findBy(['status' => 'ended'],['popularity' => 'DESC']);
//        dump($series);
        $series = $serieRepository->findBy([],["vote" =>"DESC"],300);

        //TODO Récupérer la liste des series en BDD
        return $this->render('serie/list.html.twig', ['series' => $series]);

    }

    #[Route('/{id}', name: 'show', requirements: ['id' => '\d+'])]
    public function show( $id,SerieRepository $serieRepository): Response
    {
        $serie = $serieRepository ->find($id);
        if(!$serie){
            throw $this ->createNotFoundException('serie not found');
        }
        //TODO récupération des infos de la série
        return $this->render('serie/show.html.twig',['serie' => $serie]);
    }

    #[Route('/add', name: 'add')]
    public function add(SerieRepository $serieRepository, EntityManagerInterface $entityManager): Response
    //injection de dependance entity manager en parametre
    {
        $serie = new Serie();

        $serie->setName("The Office")
            ->setBackdrop("backdrop.png")
            ->setDateCreated(new \DateTime())
            ->setGenres("Comedy")
            ->setFirstAirDate(new \DateTime(2022 - 02 - 02))
            ->setLastAirDate(new \DateTime("-6 month"))
            ->setPopularity(850.52)
            ->setPoster("poster.png")
            ->setTmdbId(123456)
            ->setVote(8.5)
            ->setStatus("Ended");


//        dump($serie);
//        $serieRepository->save($serie,true);
//        dump($serie);
//        $serie ->setName("The Last of Us");
//
//        $serieRepository->save($serie,true);
//        dump($serie);
 //      $entityManager->persist($serie);
//        $entityManager->persist($serie2);
//        $entityManager->flush();

        $serieRepository->remove($serie, true);

        dump($serie);

        //TODO Créer un formulaire d'ajout de série
        return $this->render('serie/add.html.twig');
    }


}