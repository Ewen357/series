<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/home', name: 'main_home')]
    public function home(): Response
    {
        $username = "<h1>Ewen</h1>";
        $serie = ['title' => 'Community', 'year' => 'Ouf', 'platform' => 'NBC'];

        return $this->render("main/home.html.twig", [
            //la clé devient le nom de la variable coté twig
            "name" => $username,
            "serie" => $serie
        ]);
    }

    /**
     * @Route("/test", name="main_test")
     */
    public function test(): Response
    {
        return $this->render('main/test.html.twig');
    }

//    public function truc()
//    {
//        //dans routes.yaml
//        die("Truc !");
//    }
}