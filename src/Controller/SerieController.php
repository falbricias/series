<?php

namespace App\Controller;

use App\Entity\Serie;
use App\Form\SerieType;
use App\Repository\SerieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

//Attribut de la classe qui permet de mutualiser des informations
#[Route('/serie', name: 'serie_')]

class SerieController extends AbstractController
{
    #[Route('/list/{page}', name: 'list', requirements: ['page' => '\d+'], methods: 'GET')]
    public function list(SerieRepository $serieRepository, int $page = 1): Response
    {
        //TODO Récupérer la liste des séries en DB
        //On récupère toutes les séries en passant par le repository
        //$series = $serieRepository->findAll();

        //On récupère toutes les séries de type comédie ET terminées (I.e en DB : colonne "status" = "ended" et colonne "genres" = "Comedy")
        //Utilisation de findBy avec un tableau de clause WHERE
        //$series = $serieRepository->findBy(['status' => 'returning'], ['popularity' => 'DESC'], 10, 10);

        //Récupération des 50 séries les mieux notées
        //$series = $serieRepository->findBy([],['vote' => 'DESC'], 50);

        //Exemple de méthode magique : findBy+nom de l'attribut et passer le paramètre souhaité
        //Méthode magique créée dynamiquement en fonction des attributs de l'entité associée
        //exemple 1 : $series = $serieRepository->findByName('The Office');
        //exemple 2 : $series = $serieRepository->findByStatus('ENDED');

        //Compte le nombre de lignes de séries dans la table
        $nbSerieMax = $serieRepository->count([]);
        $maxPage = ceil($nbSerieMax / SerieRepository::SERIE_LIMIT);

        if($page >= 1 && $page <= $maxPage){
            //Appel de la requête stockée dans la méthode findBestSeries du Repository
            $series = $serieRepository->findBestSeries($page);
        } else{
            throw $this->createNotFoundException('Oops ! Page not found !');
        }

        dump($series);

        //On envoie les données récupérées à la vue (i.e : en second paramètre de la méthode render
        return $this->render('serie/list.html.twig', [
            'series' => $series,
            'currentPage' => $page,
            'maxPage' => $maxPage
        ]);
    }

    #[Route('/{id}', name: 'show', requirements: ['id' => '\d+'])]
    public function show(int $id, SerieRepository $serieRepository): Response
    {
        //Récupération d'une série par son id
        $serie = $serieRepository->find($id);

        if(!$serie){
            //Lance une erreur 404 si la série n'existe pas
            throw $this->createNotFoundException("Oops ! Serie not found !");
        }

        return $this->render('serie/show.html.twig', [
            'serie' => $serie
        ]);
    }

    #[Route('/add', name: 'add')]
    #[IsGranted("ROLE_USER")]
    public function add(SerieRepository $serieRepository, EntityManagerInterface $entityManager, Request $request): Response
    {
        //Renvoie une erreur 403
        //$this->createAccessDeniedException('message');

        $serie = new Serie();

        //1 - Création d'une instance de form lié à une instance de Série
        $serieForm = $this->createForm(SerieType::class, $serie);

        //2 - Méthode qui extrait les éléments du formulaire de la requête
        $serieForm->handleRequest($request);

        //3 - Traitement si le formulaire est soumis et valide (valide au regard des contraintes de validation des attributs de l'entité)
        if ($serieForm->isSubmitted() && $serieForm->isValid()){

            //Gestion de l'upload de photo d'une série nouvellement créée
            /**
             * @var UploadedFile $file
             */
            $file = $serieForm->get('poster')->getData();
            //Détermine un nom au document téléchargé (avec un ID aléatoire généré + concat de l'extension)
            $newFileName = $serie->getName() . '-' . uniqid() . '.' . $file->guessExtension();
            //Déplace le fichier chargé dans le répertoire indiqué et renommé avec le $newFileName
            $file->move('img/posters/series', $newFileName);
            //Sauvegarde du nom du fichier en DB
            $serie->setPoster($newFileName);

            //Sauvegarde en DB la nouvelle série saisie par l'utilisateur
            $serieRepository->save($serie, true);

            //Message flash d'info d'ajout de la série OK
            $this->addFlash('success', 'Serie added !');

            //Redirige vers la page de détail de la série
            return $this->redirectToRoute('serie_show', ['id' => $serie->getId()]);
        }

        dump($serie);

        return $this->render('serie/add.html.twig', [
            'serieForm' => $serieForm->createView()
        ]);
    }

    #[Route('/remove/{id}', name: 'remove')]
    public function remove(int $id, SerieRepository $serieRepository){
        //Récupération de la série
        $serie = $serieRepository->find($id);

        if($serie){
            //Suppression de la série si elle existe
            $serieRepository->remove($serie, true);
            $this->addFlash('warning', 'Serie deleted !');
        }else{
            throw $this->createNotFoundException('This serie cant be deleted !');
        }

        return $this->redirectToRoute('serie_list');
    }

}