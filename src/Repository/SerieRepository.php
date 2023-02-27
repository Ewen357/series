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
        //En DQL
        //recuperation des series avec un vote sup a 8 et une popu sup a 100 ordonné par popu

//        $dql = "SELECT s FROM App\Entity\Serie s
//                WHERE s.vote > 8
//                AND s.popularity > 100
//                ORDER BY s.popularity DESC";
//
//        $query = $this -> getEntityManager()->createQuery($dql);
//        $query ->setMaxResults(50);
//        return $query ->getResult();

        //En query Builder



        $offset =($page - 1) * self::SERIE_LIMIT;

        $qb = $this->createQueryBuilder('s');
        $qb //jointure sur les attributs d'instance
            ->leftJoin("s.seasons", "sea")
            //recuperation des colonnes de la jointure
            ->addOrderBy('s.popularity','DESC')
            ->addSelect("sea")
//            ->andWhere('s.vote > 8')
//            ->andWhere('s.popularity > 100')
            ->setFirstResult($offset)
            ->setMaxResults(self::SERIE_LIMIT);

        $query = $qb->getQuery();
        //permet de gerer les offset avec jointure
        $paginator = new Paginator($query);


        return $paginator;
    }
}
