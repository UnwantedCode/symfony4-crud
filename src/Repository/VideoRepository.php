<?php

namespace App\Repository;

use App\Entity\Video;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Video|null find($id, $lockMode = null, $lockVersion = null)
 * @method Video|null findOneBy(array $criteria, array $orderBy = null)
 * @method Video[]    findAll()
 * @method Video[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VideoRepository extends ServiceEntityRepository
{
    private $paginator;
    public function __construct(\Doctrine\Persistence\ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Video::class);
        $this->paginator = $paginator;
    }

    public function findByChildIds(array $value, int $page, ?string $sort_method)
    {
        if($sort_method != 'rating')
        {
            $dbquery = $this->createQueryBuilder('v')
                ->andWhere('v.category IN (:val)')
                ->leftJoin('v.comments', 'c')
                ->leftJoin('v.usersThatLike', 'l')
                ->leftJoin('v.usersThatDontLike', 'd')
                ->addSelect('c','l','d')
                ->setParameter('val', $value)
                ->orderBy('v.title', $sort_method);
        }
        else
        {
            $dbquery =  $this->createQueryBuilder('v')
                ->addSelect('COUNT(l) AS HIDDEN likes') // bez hidden zwróci array: count + entity
                ->leftJoin('v.usersThatLike', 'l')
                ->andWhere('v.category IN (:val)')
                ->setParameter('val', $value)
                ->groupBy('v')
                ->orderBy('likes', 'DESC');
        }

        $dbquery->getQuery();


        $pagination = $this->paginator->paginate($dbquery, $page, Video::perPage);
        return $pagination;
    }

    public function findByTitle(string $query, int $page, ?string $sort_method)
    {

        $querybuilder = $this->createQueryBuilder('v');
        $searchTerms = $this->prepareQuery($query);

        foreach ($searchTerms as $key => $term)
        {
            $querybuilder
                ->orWhere('v.title LIKE :t_'.$key)
                ->setParameter('t_'.$key, '%'.trim($term).'%');
        }

        if($sort_method != 'rating')
        {
            $dbquery =  $querybuilder
                ->orderBy('v.title', $sort_method)
                ->leftJoin('v.comments', 'c')
                ->leftJoin('v.usersThatLike', 'l')
                ->leftJoin('v.usersThatDontLike', 'd')
                ->addSelect('c','l','d')
                ;
        }
        else
        {
            $dbquery =  $querybuilder
                ->addSelect('COUNT(l) AS HIDDEN likes') // bez hidden zwróci array: count + entity
                ->leftJoin('v.usersThatLike', 'l')
                ->groupBy('v')
                ->orderBy('likes', 'DESC')
                ;
        }
        $dbquery->getQuery();
        return $this->paginator->paginate($dbquery, $page, Video::perPage);
    }

    private function prepareQuery(string $query): array
    {
        $terms = array_unique(explode(' ',$query));
        $terms = array_filter($terms, function ($term){
            return 2 <= mb_strlen($term);
        });
        return $terms;
    }

    public function videoDetails($id)
    {
        return $this->createQueryBuilder('v')
            ->leftJoin('v.comments', 'c')
            ->leftJoin('c.user', 'u')
            ->addSelect('c', 'u')
            ->andWhere('v.id = :val')
            ->setParameter('val', $id)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    // /**
    //  * @return Video[] Returns an array of Video objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Video
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
