<?php

namespace App\Repository;

use App\Entity\Serie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Serie>
 *
 * @method Serie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Serie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Serie[]    findAll()
 * @method Serie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SerieRepository extends ServiceEntityRepository
{
    const SERIE_LIMIT = 50;


    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Serie::class);
    }

    public function save(Serie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Serie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findBestSeries(int $page){

        // page 1 -> 0 à 49
        // page 2 -> 50 à 99

        $offset = ($page - 1) * self::SERIE_LIMIT;


        //1 - En DQL
        //Requête de récupération des séries avec un vote > 8 et une popularité > 100 ordonné par popularité
//        $dql = 'SELECT s FROM App\Entity\Serie as s
//                WHERE s.vote > 8
//                AND s.popularity > 100
//                ORDER BY s.popularity DESC';
//
//        //Transforme le String en objet de requête
//        $query = $this->getEntityManager()->createQuery($dql);
//
//        //Ajout d'une limite du nombre de résultats
//        $query->setMaxResults(50);


        //2 - Même requête en QueryBuilder
        $qb = $this->createQueryBuilder('s');
        $qb
            //Jointure sur les attributs d'instance
            ->leftJoin("s.seasons", "sea")
            //Récupération des colonnes de la jointure
            ->addSelect("sea")
            ->addOrderBy('s.popularity', 'DESC')
//            ->andWhere('s.vote > 8')
//            ->andWhere('s.popularity > 100')
            ->setFirstResult($offset)
            ->setMaxResults(self::SERIE_LIMIT);

        $query = $qb->getQuery(); //revoie une instance de Query

        //Permet de gérer les offset avec jointure
        $paginator = new Paginator($query);

        return $paginator;
    }

}
