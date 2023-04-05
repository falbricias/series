<?php

namespace App\Controller;

use App\Entity\Season;
use App\Form\SeasonType;

use App\Repository\SeasonRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/season', name: 'season_')]
class SeasonController extends AbstractController
{
    #[Route('/add', name: 'add')]
    public function add(Request $request, SeasonRepository $seasonRepository): Response
    {
        $season = new Season();

        //1 - Création d'une instance de form lié à une instance de Season
        $seasonForm = $this->createForm(SeasonType::class, $season);

        //2 - Méthode qui extrait les éléments du formulaire de la requête
        $seasonForm->handleRequest($request);



        //3 - Traitement si le formulaire est soumis et valide (valide au regard des contraintes de validation des attributs de l'entité)
        if($seasonForm->isSubmitted() && $seasonForm->isValid()){
            //Sauvegarde en DB la nouvelle série saisie par l'utilisateur
            $seasonRepository->save($season, true);

            //Message flash d'info d'ajout de la série OK
            $this->addFlash('success', 'Season added !');

            //Redirige vers la page de détail de la série
            return $this->redirectToRoute('serie_show', ['id' => $season->getSerie()->getId()]);

        }

        return $this->render('season/add.html.twig', [
            'seasonForm' => $seasonForm->createView()
        ]);
    }

}
