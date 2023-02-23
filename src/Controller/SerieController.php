<?php

namespace App\Controller;

use App\Entity\Serie;
use App\Form\SerieType;
use App\Repository\SerieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

//Attribut de la classe qui permet de mutualiser des informations
#[Route('/serie', name: 'serie_')]

class SerieController extends AbstractController
{
    #[Route('/list', name: 'list', methods: 'GET')]
    public function list(SerieRepository $serieRepository): Response
    {
        //TODO Récupérer la liste des séries en DB
        //On récupère toutes les séries en passant par le repository
        $series = $serieRepository->findAll();

        //On récupère toutes les séries de type comédie ET terminées (I.e en DB : colonne "status" = "ended" et colonne "genres" = "Comedy")
        //Utilisation de findBy avec un tableau de clause WHERE
        //$series = $serieRepository->findBy(['status' => 'returning'], ['popularity' => 'DESC'], 10, 10);

        //Récupération des 50 séries les mieux notées
        //$series = $serieRepository->findBy([],['vote' => 'DESC'], 50);

        //Exemple de méthode magique : findBy+nom de l'attribut et passer le paramètre souhaité
        //Méthode magique créée dynamiquement en fonction des attributs de l'entité associée
        //exemple 1 : $series = $serieRepository->findByName('The Office');
        //exemple 2 : $series = $serieRepository->findByStatus('ENDED');

        //Appel de la requête stockée dans la méthode findBestSeries du Repository
        $series = $serieRepository->findBestSeries();

        dump($series);

        //On envoie les données récupérées à la vue (i.e : en second paramètre de la méthode render
        return $this->render('serie/list.html.twig', [
            'series' => $series
        ]);
    }

    #[Route('/{id}', name: 'show', requirements: ['id' => '\d+'])]
    public function show(int $id, SerieRepository $serieRepository): Response
    {
        $serie = $serieRepository->find($id);

        if(!$serie){
            //Lance une erreur 404 si la série n'existe pas
            throw $this->createNotFoundException('Oops ! This serie does not exist ! Not found exception !');
        }

        dump($serie);

        //TODO Récupération des infos de la série
        return $this->render('serie/show.html.twig', [
            'serie' => $serie
        ]);
    }

    #[Route('/add', name: 'add')]
    public function add(SerieRepository $serieRepository, EntityManagerInterface $entityManager, Request $request): Response
    {
//        $serie = new Serie();

//        //Settage des infos de la série
//        $serie
//            ->setName("Le magicien")
//            ->setBackdrop("backdrop.png")
//            ->setDateCreated(new \DateTime())
//            ->setGenres("Coemdy")
//            ->setFirstAirDate(new \DateTime('2005-03-24'))
//            ->setLastAirDate(new \DateTime('-6 month'))
//            ->setPopularity(850.52)
//            ->setPoster("poster.png")
//            ->setTmdbId(123456)
//            ->setVote(8.5)
//            ->setStatus("ended");
//
//        //Utilisation directement de l'Entity Manager
//        $entityManager->persist($serie);
//        $entityManager->flush(false);

//        dump($serie);
//
//        //Enregistrement de la série settée ci-dessus en DB
//        $serieRepository->save($serie, false);
//
//        dump($serie);
//
//        $serie->setName('The last of us');
//        $serieRepository->save($serie, true);
//
//        dump($serie);

        $serie = new Serie();

        //Création d'une instance de form lié à une instance de Série
        $serieForm = $this->createForm(SerieType::class, $serie);

        //Méthode qui extrait les éléments du formulauire de la requête
        $serieForm->handleRequest($request);

        if ($serieForm->isSubmitted()){
            //Sette manuellement la date de création
            $serie->setDateCreated(new \DateTime());

            //Sauvegarde en DB la nouvelle série saisie par l'utilisateur
            $serieRepository->save($serie, true);
        }

        dump($serie);

        //TODO Créer un formulaire d'ajout de série
        return $this->render('serie/add.html.twig', [
            'serieForm' => $serieForm->createView()
        ]);
    }

}