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
        $username = 'Arthur';
        $serie = ['Title' => 'Community', 'Year' => 2009, 'Platform' => 'NBC'];

        return $this->render("main/home.html.twig", [
            'name' => $username,
            'serie' => $serie
        ]);
    }

    /**
     * @Route("/test", name="main_test")
     */
    public function test(){

        return $this->render("main/test.html.twig");

    }


}
