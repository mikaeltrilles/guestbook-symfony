<?php

namespace App\Repository;

use App\Entity\Conference;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Conference>
 *
 * @method Conference|null find($id, $lockMode = null, $lockVersion = null)
 * @method Conference|null findOneBy(array $criteria, array $orderBy = null)
 * @method Conference[]    findAll()
 * @method Conference[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConferenceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Conference::class);
    }

    public function add(Conference $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Conference $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Conference[] Returns an array of Conference objects
//     */
    public function findByYear($value): array
    {
        return $this->createQueryBuilder('conf')
           ->andWhere('conf.year = :annee')
           ->setParameter('annee', $value)
           ->orderBy('conf.year', 'ASC')
           ->setMaxResults(10)
           ->getQuery()
           ->getResult()
       ;
    }

    public const PAGINATOR_PER_PAGE_CONF = 4;

    public function getConferencePaginator(int $offset): Paginator
    {
        $query = $this->createQueryBuilder('c')
            ->orderBy('c.year', 'DESC')
            ->addOrderBy('c.city')
            ->setMaxResults(self::PAGINATOR_PER_PAGE_CONF)
            ->setFirstResult($offset)
            ->getQuery()
            ;
        return new Paginator($query);
    }



//    public function findOneBySomeField($value): ?Conference
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
