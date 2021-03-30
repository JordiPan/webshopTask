<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function findAllOrdered()
    {
//        $dql = 'SELECT product FROM App\Entity\Product product ORDER BY product.name ASC';
//        $query = $this->getEntityManager()->createQuery($dql);
//        return $query->execute();

        $qb = $this->createQueryBuilder('p')->addOrderBy('p.name','ASC');
        $query = $qb->getQuery();
        return $query->execute();
    }

    public function search($term)
    {
        return $this->createQueryBuilder('p')
            ->where('p.name LIKE :searchTerm')->join('p.category','cat')
            ->setParameter('searchTerm', '%'.$term.'%')
            ->getQuery()->getResult();
    }
    public function join() {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.category','c')
            ->where('c.parent IS null')
//            ->setParameters(['name' => $name])
            ->getQuery()->getResult();
    }
    public function f() {
        return$this->createQueryBuilder('p')
            ->leftJoin('p.discountProducts', 'dp')
            ->where('dp.product IS not NULL')
            ->getQuery()->getResult();
    }
    public function u() {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.discountProducts', 'dp')
            ->leftJoin('dp.discount', 'ds')
            ->where('ds.percentage > 50')
            ->getQuery()->getResult();
    }
    public function c() {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.discountProducts', 'dp')
            ->join('dp.discount', 'dc')
            ->select('MAX(dc.percentage) as pe')
            ->getQuery()->getOneOrNullResult();
    }
    public function ProductHighestPercentage($id) {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.category', 'cat')
            ->leftJoin('p.discountProducts', 'dp')
            ->leftJoin('dp.discount', 'd')
            ->where('cat.id = :id')
            ->andWhere('d.percentage=' .$this->productMax($id)['percent'])
            ->setParameter('id', $id)
            ->getQuery()->getResult();
    }
    public function productMax($id) {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.category', 'cat')
            ->leftJoin('p.discountProducts', 'dp')
            ->leftJoin('dp.discount', 'd')
            ->where('cat.id = :id')
            ->setParameter('id', $id)
            ->select('MAX(d.percentage) as percent')
            ->getQuery()->getOneOrNullResult();
    }
    public function categoryProducts($id) {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.category','cat')
            ->where('cat.id = :id')
            ->setParameter('id', $id)
            ->orderBy('p.name','ASC')
            ->getQuery()->getResult();
    }

// /**
    //  * @return Product[] Returns an array of Product objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Product
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
