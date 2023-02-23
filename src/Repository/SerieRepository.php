<?php

namespace App\Repository;

use App\Entity\Serie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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

    public function findBestSeries(){
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
            ->addOrderBy('s.popularity', 'DESC')
            ->andWhere('s.vote > 8')
            ->andWhere('s.popularity > 100')
            ->setMaxResults(50);

        $query = $qb->getQuery(); //revoie une instance de Query

        return $query->getResult();
    }

}
