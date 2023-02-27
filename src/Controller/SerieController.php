<?php

namespace App\Controller;

use App\Entity\Serie;
use App\Form\SerieType;
use App\Repository\SerieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

//attribut de la class qui permet de mutualiser des informations
#[Route('/serie', name: 'serie_')]
class SerieController extends AbstractController
{
    #[Route('/list/{page}', name: 'list',requirements: ['page'=> '\d+'],methods: "GET")]
    public function list(SerieRepository $serieRepository, int $page = 1): Response
    {
        //$series = $serieRepository -> findAll();
        //dump($series);
        $series = $serieRepository->findBy(['status' => 'ended'],['popularity' => 'DESC']);
        //dump($series);
        //$series = $serieRepository->findBy([],["vote" =>"DESC"],300);
        //ou en plus rapide
        //$series = $serieRepository ->findByStatus("ended");

        //nombre de series dans ma table
        $nbSerieMax = $serieRepository->count([]);
        $maxPage =ceil($nbSerieMax / $serieRepository::SERIE_LIMIT );

        if($page >= 1 && $page <= $maxPage ){
            $series = $serieRepository->findBestSeries($page);

        }else throw $this->createNotFoundException("Page not found !");

        dump($series);


        return $this->render('serie/list.html.twig', ['series' => $series,'currentPage' => $page,'maxPage'=>$maxPage]);

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
    public function add(SerieRepository $serieRepository, Request $request): Response
    //injection de dependance entity manager en parametre
    {
        $serie = new Serie();

//        $serie->setName("The Office")
//            ->setBackdrop("backdrop.png")
//            ->setDateCreated(new \DateTime())
//            ->setGenres("Comedy")
//            ->setFirstAirDate(new \DateTime(2022 - 02 - 02))
//            ->setLastAirDate(new \DateTime("-6 month"))
//            ->setPopularity(850.52)
//            ->setPoster("poster.png")
//            ->setTmdbId(123456)
//            ->setVote(8.5)
//            ->setStatus("Ended");
//
//
////        dump($serie);
////        $serieRepository->save($serie,true);
////        dump($serie);
////        $serie ->setName("The Last of Us");
////
////        $serieRepository->save($serie,true);
////        dump($serie);
// //      $entityManager->persist($serie);
////        $entityManager->persist($serie2);
////        $entityManager->flush();
//
//        $serieRepository->remove($serie, true);
//
//        dump($serie);

        $serieForm = $this->createForm(SerieType::class, $serie);
        $serieForm ->handleRequest($request);

        if ($serieForm->isSubmitted()&& $serieForm ->isValid()){
            /**
             * @var UploadedFile $file
             */
            $file =$serieForm->get('poster')->getData();
            $newFileName = $serie->getName() ."-".uniqid()."-".$file->guessExtension();
            $file->move('img/posters/series', $newFileName);
            $serie->setPoster($newFileName);

            dd($file);
            $serieRepository->save($serie,true);
            $this->addFlash("warning","Serie Added !!");
            return $this ->redirectToRoute('serie_show',['id'=> $serie->getId()]);
        }

        dump($serie);

        //TODO Créer un formulaire d'ajout de série
        return $this->render('serie/add.html.twig',['serieForm'=>$serieForm->createView()]);
    }

    #[Route('/remove/{id}', name: 'remove')]
    public function remove(int $id,SerieRepository $serieRepository){
        $serie = $serieRepository ->find($id);
        if ($serie) {
            $serieRepository->remove($serie, true);
            $this->addFlash('success','Serie deleted !');
        }else{
            throw $this ->createNotFoundException("this serie can't be deleted");
        }
        return $this->redirectToRoute('serie_list');
    }

}