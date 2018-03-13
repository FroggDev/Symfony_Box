<?php

namespace App\Repository;

use App\Entity\Box;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;


/**
 * @method Box|null find($id, $lockMode = null, $lockVersion = null)
 * @method Box|null findOneBy(array $criteria, array $orderBy = null)
 * @method Box[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BoxRepository extends ServiceEntityRepository
{

    private $allBox;

    /**
     * AuthorRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Box::class);
    }

    /**
     * @return array
     */
    public function findAll(): array
    {
        if($this->allBox){
            return $this->allBox;
        }

        $this->allBox = $this->createQueryBuilder('b')
            ->orderBy('b.dateCreation', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->allBox;
    }
}