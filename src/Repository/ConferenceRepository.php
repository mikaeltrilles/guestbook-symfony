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
        ->orderBy('conf.year', 'DESC')
        ->addOrderBy('conf.city', 'ASC')
        ->setMaxResults(10)
        ->getQuery()
        ->getResult()
    ;
    }

    public const PAGINATOR_PER_PAGE_CONF = 4;
    // je recherche les conférences par année et par ville, je fais une requête avec une jointure
    public function getConferencePaginator(int $offset, string $year, string $city): Paginator
    {
        $query = $this->createQueryBuilder('c');
        if ($year) {
            $query->andWhere('c.year = :year')
        ->setParameter('year', $year);
        }
        if ($city) {
            $query->andWhere('c.city = :city')
        ->setParameter('city', $city);
        }

        $query->setMaxResults(self::PAGINATOR_PER_PAGE_CONF)
        ->setFirstResult($offset)
        ->orderBy('c.year', 'ASC')
        ->addOrderBy('c.city', 'ASC')
        ->getQuery()
            ;
        return new Paginator($query);
    }
    

    public function getListYear()
    {
        $years = [];
        foreach ($this->createQueryBuilder('c')
            ->select('c.year')
            ->distinct(true)
            ->orderBy('c.year', 'ASC')
            ->getQuery()
            ->getResult() as $cols) {
            $years[] = $cols['year'];
        }
        return $years;
    }

    // fonction qui liste les villes
    public function getListCity()
    {
        $cities = [];
        foreach ($this->createQueryBuilder('c')
            ->select('c.city')
            ->distinct(true)
            ->orderBy('c.city', 'ASC')
            ->getQuery()
            ->getResult() as $cols) {
            $cities[] = $cols['city'];
        }
        return $cities;
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
