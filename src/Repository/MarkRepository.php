<?php

namespace App\Repository;

use App\Entity\Mark;
use App\Entity\Recipe;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Mark>
 *
 * @method Mark|null find($id, $lockMode = null, $lockVersion = null)
 * @method Mark|null findOneBy(array $criteria, array $orderBy = null)
 * @method Mark[]    findAll()
 * @method Mark[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MarkRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mark::class);
    }
}
