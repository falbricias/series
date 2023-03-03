<?php

namespace App\Controller\Api;

use App\Entity\Serie;
use App\Repository\SerieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/serie', name: 'api_serie_')]
class SerieController extends AbstractController
{
    #[Route('', name: 'retrieve_all', methods: 'GET')]
    public function retrieveAll(SerieRepository $serieRepository): Response
    {
        $series = $serieRepository->findAll();

//        /* json_encode : transforme une chaîne de car ou un tableau de chaîne de car en json
//        mais ne permet pas de tranformer un objet en json (voir méthode json() ic-dessous pour encoder un objet */
//        $series = json_encode($series);

        /* Retourne une json response qui utilise le serializer component pour encoder un objet en json
            => i.e : renvoie les données au format json en utilisant le "groups" dans les entités concernées */
        return $this->json($series, 200, [], ['groups' => 'serie_api']);
    }

    #[Route('{/id}', name: 'retrieve_one', methods: 'GET')]
    public function retrieveOne(int $id, SerieRepository $serieRepository): Response
    {
        //Exécute la requête find by id du répo
        $serie = $serieRepository->find($id);

        // Revoie la série choisie au format json
        return $this->json($serie, 200, [], ['groups' => 'serie_api']);
    }

    #[Route('', name: 'add', methods: 'POST')]
    public function add(Request $request, SerializerInterface $serializer): Response
    {
        /* 1 - Créer un objet de type Request
        Attention : l'objet Request passé en attribut doit être de type "HttpFoundation" */
        $data = $request->getContent();

        /*  2 - Récupérer la donnée au format Json
        Récupère la chaine de car en json et crée un objet Série depuis la variable $data
        i.e : Serializer permet de transformer la donnée JSON en instance de série */
        $serie = $serializer->deserialize($data, Serie::class,'json');

        /* 3 - Sauvegarder l'objet désérialisé en DB en appelant
        le repository et en faisant un save de notre objet Série */
        //TODO : sauvegarder l'objet en DB

        return $this->json("OK");
    }

    #[Route('/{id}', name: 'remove', methods: 'DELETE')]
    public function remove(): Response
    {
        //TODO : delete this serie
    }

    #[Route('/{id}', name: 'update', methods: 'PUT')]
    public function update(int $id, Request $request, SerieRepository $serieRepository): Response
    {
        $serie = $serieRepository->find($id);

        //Récupération du corps de la requête
        $data = $request->getContent();
        //Utilisation du json_decode pour transformer le json en objet anonyme
        $data = json_decode($data);

        //Modification du nombre de likes
        if($data->like){
            $serie->setNbLike($serie->getNbLike() + 1);
        }else{
            $serie->setNbLike($serie->getNbLike() - 1);
        }

        //Enregistrement en DB
        $serieRepository->save($serie, true);

        return $this->json(['nbLike' => $serie->getNbLike()]);
    }
}
